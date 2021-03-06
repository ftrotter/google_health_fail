if(!dojo._hasResource["dojox.cometd.HttpChannels"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojox.cometd.HttpChannels"] = true;
dojo.provide("dojox.cometd.HttpChannels");
 
dojo.require("dojox.rpc.Client");
dojo.require("dojox.io.httpParse");
if(dojox.data && dojox.data.JsonRestStore){
	dojo.require("dojox.data.restListener");
}
// Note that cometd _base is _not_ required, this can run standalone, but ifyou want 
// cometd functionality, you must explicitly load/require it elsewhere, and cometd._base
// MUST be loaded prior to HttpChannels ifyou use it.

// summary:
// 		HTTP Channels - An HTTP Based approach to Comet transport with full HTTP messaging 
// 		semantics including REST
// 		HTTP Channels is a efficient, reliable duplex transport for Comet

// description:
// 		This can be used:
// 		1. As a cometd transport
// 		2. As an enhancement for the REST RPC service, to enable "live" data (real-time updates directly alter the data in indexes)
// 		2a. With the JsonRestStore (which is driven by the REST RPC service), so this dojo.data has real-time data. Updates can be heard through the dojo.data notification API.
// 		3. As a standalone transport. To use it as a standalone transport looks like this:
// 	|		dojox.cometd.HttpChannels.open();
// 	|		dojox.cometd.HttpChannels.get("/myResource",{callback:function(){
// 	|			// this is called when the resource is first retrieved and any time the 
// 	|			// resource is changed in the future. This provides a means for retrieving a
// 	|			// resource and subscribing to it in a single request
// 	|		});
// 	|	dojox.cometd.HttpChannels.subscribe("/anotherResource",{callback:function(){
// 	|		// this is called when the resource is changed in the future
// 	|	});
// 		Channels HTTP can be configured to a different delays:
// 	|	dojox.cometd.HttpChannels.autoReconnectTime = 60000; // reconnect after one minute
//

(function(){
	var Channels = dojox.cometd.HttpChannels = {
		absoluteUrl: function(relativeUrl){
			return new dojo._Url(location.href,relativeUrl)+'';
		},
		acceptType: "x-application/http+json,application/http;q=0.9,*/*;q=0.7",
		subscriptions: {},
		subCallbacks: {},
		autoReconnectTime: 30000,
		sendAsJson: false,
		url: '/channels',
		open: function(){
			// summary:
			// 		Startup the transport (connect to the "channels" resource to receive updates from the server).
			//
			// description:
			//		Note that if there is no connection open, this is automatically called when you do a subscription,
			// 		it is often not necessary to call this
			//
			if(!this.connected){
				var xdr = dojox.cometd.useXDR && window.XDomainRequest;
				// we are currently directly using the XHR or XDR object, because Dojo's XHR wrapper is not architected for progress events. If that changes, we can use Dojo's XHR here.
				this.xhr = (window.XMLHttpRequest && new (xdr || XMLHttpRequest)) || new ActiveXObject("Microsoft.XMLHTTP");
				var xhr = this.xhr;
				this.connectionId = dojox._clientId;
				var clientIdHeader = this.started ? 'Client-Id' : 'Create-Client-Id';
				// post to our channel url
				if(xdr){
					xhr.open("POST",this.absoluteUrl(this.url + "?http-Accept=" + this.acceptType + "&http-" + clientIdHeader + "=" + this.connectionId)); 
				}else{// xdr doesn't have setRequestHeader, and it must have an absolute url
					xhr.open("POST",this.url,true);
					xhr.setRequestHeader('X-' + clientIdHeader,this.connectionId); 
	  				// let the server know what type of response we can accept
			  		xhr.setRequestHeader('Accept',this.acceptType);
				}
		  		var self = this;
		  		this.lastIndex = 0; 
				var onstatechange = function(){ // get all the possible event handlers
					var error,loaded,data;
			        try{
						if(!xhr.readyState || xhr.readyState > 2){// only if"OK" (xdr doesn't have a readyState)
							self.readyState = xhr.readyState;
							error = xhr.status > 300;		
							data = xhr.responseText.substring(self.lastIndex);
				        	loaded = !error && typeof data=='string';//firefox can throw an exception right here
						}
			        } catch(e){
			        	error = xhr.readyState == 4; // an error in ready state 3 is common with IE's old API. But in ready state 4, it is an indication of a real error in firefox 
			        }
			        if(typeof dojo == 'undefined'){
			        	return;// this can be called after dojo is unloaded, just do nothing in that case
			        }
			        if(loaded){ 
			        	var contentType = xhr.contentType || xhr.getResponseHeader("Content-Type");
			        	self.started = true;
						try{			        	
			        		error = self.onprogress(xhr,xdr,data,contentType);
						}
				        finally {
							if(xhr.readyState==4){
					        	xhr = null;
					        	if(self.connected){
					        		self.connected = false;
					        		self.open();
					        	}
							}
				        }
			        }
			        if(error){ // an error has occurred
			        
			        	if(self.started){ // this means we need to reconnect
			        		self.started = false;
							self.connected = false;
			        		var subscriptions = self.subscriptions;
			        		self.subscriptions = {};
							for(var i in subscriptions){
								self.subscribe(i,{since:subscriptions[i]});
							}
			        	}else{
			        		self.disconnected();
			        	}
			        }
			  	};
	  			xhr.onreadystatechange = onstatechange;
		  		if(xdr){
			  		xhr.onerror = function(){
			  			xhr.readyState = 4;
			  			xhr.status = 404; // this is so that we can restartup ifnecessary
			  			onstatechange();
			  		};
			  		xhr.onload = function(){
			  			xhr.readyState = 4;
			  			onstatechange();
			  		};
		  			xhr.onprogress = onstatechange;
		  		}
	  			 
				xhr.send(null);
				if(window.attachEvent){// IE needs a little help with cleanup
					attachEvent("onunload",function(){
						self.connected= false;
						if(xhr){
							xhr.abort();
						}
					});
				}
				
				this.connected = true;
			}
		},
		_send: function(method,args,data){
			// fire an XHR with appropriate modification for JSON handling
			if(this.sendAsJson){
				// send use JSON Messaging
				args.postBody = dojo.toJson({
					target:args.url,
					method:method,
					content: data,
					params:args.content,
					subscribe:headers["X-Subscribe"]
				});
				args.url = this.url;
				method = "POST";
			}else{
				args.postData = dojo.toJson(data);
			}			
			return dojo.xhr(method,args,args.postBody);
		}, 
		subscribe: function(/*String*/channel, /*dojo.__XhrArgs?*/args){
			// summary:
			// 		Subscribes to a channel/uri, and returns a dojo.Deferred object for the response from 
			// 		the subscription request
			//
			// channel: 
			// 		the uri for the resource you want to monitor
			// 
			// args: 
			// 		See dojo.xhr
			// 
			// headers:
			// 		These are the headers to be applied to the channel subscription request
			//
			// callback:
			// 		This will be called when a event occurs for the channel
			// 		The callback will be called with a single argument:
			// 	|	callback(message)
			// 		where message is an object that follows the XHR API:
			// 		status : Http status
			// 		statusText : Http status text
			// 		getAllResponseHeaders() : The response headers
			// 		getResponseHeaders(headerName) : Retrieve a header by name
			// 		responseText : The response body as text
			// 			with the following additional Bayeux properties 
			// 		data : The response body as JSON
			// 		channel : The channel/url of the response
			args = args || {};
			args.url = this.absoluteUrl(channel);
			if(args.headers){ 
				// FIXME: combining Ranges with notifications is very complicated, we will save that for a future version
				delete args.headers.Range;
			}
			var oldSince = this.subscriptions[channel];
			var method = args.method || "HEAD"; // HEAD is the default for a subscription
			var since = args.since;
			var callback = args.callback;
			var headers = args.headers || (args.headers = {});
			this.subscriptions[channel] = since || oldSince || 0;
			var oldCallback = this.subCallbacks[channel];
			if(callback){
				this.subCallbacks[channel] = oldCallback ? function(m){
					oldCallback(m);
					callback(m);
				} : callback;
			} 
			if(!this.connected){
				this.open();
			}
			if(oldSince === undefined || oldSince != since){
				headers["Cache-Control"] = "max-age=0";
				since = typeof since == 'number' ? new Date(since).toUTCString() : since;
				if(since){
					headers["X-Subscribe-Since"] = since;
				}
				headers["X-Subscribe"] = args.unsubscribe ? 'none' : '*';
				var dfd = this._send(method,args);
				
				var self = this;
				dfd.addBoth(function(result){					
					var xhr = dfd.ioArgs.xhr;
					if(xhr.status < 400){
						if(args.confirmation){
							args.confirmation();
						}
					}
					if(xhr.getResponseHeader("X-Subscribed")  == "OK"){
						var lastMod = xhr.getResponseHeader('Last-Modified');
//							log("lastMod " + lastMod);
						
						if(xhr.responseText){ 
							self.subscriptions[channel] = lastMod || new Date().toUTCString();
						}else{
							return null; // don't process the response, the response will be received in the main channels response
						}
					}else{ // ifit is not a 202 response, that means it is did not accept the subscription
						delete self.subscriptions[channel];
					}
					if(xhr.status < 300){
						var message = {
							responseText:xhr.responseText,
							channel:channel,
							getResponseHeader:function(name){
								return xhr.getResponseHeader(name);
							},
							getAllResponseHeaders:function(){
								return xhr.getAllResponseHeaders();
							}
						};
						try{
							message.data = dojo.fromJson(message.responseText);
						}
						catch (e){}
						if(self.subCallbacks[channel]){
							self.subCallbacks[channel](message); // call with the fake xhr object
						}
					}else{
						if(self.subCallbacks[channel]){
							self.subCallbacks[channel](xhr); // call with the actual xhr object
						}
					}
					return result;
				});
				return dfd;
			}
			return null;
		},
		publish: function(channel,data){
			// summary:
			//		Publish an event.
			// description:
			// 		This does a simple POST operation to the provided URL,
			// 		POST is the semantic equivalent of publishing a message within REST/Channels
			// channel:
			// 		Channel/resource path to publish to
			// data:
			//		data to publish
			return this._send("POST",{url:channel,contentType : 'application/json'},data);
		},
		_processMessage: function(message){
//			console.log("process message ",message);
			message.event = message.event || message.getResponseHeader('X-Event');
			if(message.event=="connection-conflict"){
				return "conflict"; // indicate an error
			}
			try{
				message.data = message.content || dojo.fromJson(message.responseText);
			}
			catch(e){}
			var self = this;	
			message.channel = message.target || message.getResponseHeader('Content-Location');//for cometd
			var loc = new dojo._Url(location.href,message.channel); // TODO: more robust URL matching
			if(loc in this.subscriptions){
				this.subscriptions[loc] = message.getResponseHeader('Last-Modified'); 
			}
			if(this.subCallbacks[loc]){
				setTimeout(function(){ //give it it's own stack 
					self.subCallbacks[loc](message);
				},0);
			}
			this.receive(message);
			return null;		
		},
		onprogress: function(xhr,xdr,data,contentType){
			// internal XHR progress handler
			if(contentType.match(/application\/http\+json/)){
				var size = data.length;
				data = data.replace(/^\s*[,\[]?/,'['). // must start with a opening bracket
					replace(/[,\]]?\s*$/,']'); // and end with a closing bracket
				try{
					// if this fails, it probably means we have an incomplete JSON object
					var xhrs = dojo.fromJson(data);
					this.lastIndex += size;
				}
				catch(e){
				}
			}
			else if(contentType.match(/application\/http/)){
				// do HTTP tunnel parsing
				var topHeaders = '';
				if(!xdr){
					// mixin/inherit headers from the container response
		    		topHeaders = xhr.getAllResponseHeaders();
				}
				xhrs = dojox.io.httpParse(data,topHeaders,xhr.readyState != 4);
			}
			if(xhrs){
				for(var i = 0;i < xhrs.length;i++){
					if(this._processMessage(xhrs[i])){
						return "conflict";
					}
				}
				return null;
			}
			if(xhr.readyState != 4){ // we only want finished responses here if we are not streaming 
				return null;
			}
			if(xhr.__proto__){// firefox uses this property, so we create an instance to shadow this property
				xhr = {channel:"channel",__proto__:xhr};
			}			
			return this._processMessage(xhr);
		
		},
		
		get: function(/*String*/channel, /*dojo.__XhrArgs?*/args){
			// summary:
			// 		GET the initial value of the resource and subscribe to it  
			//		See subscribe for parameter values
			(args = args || {}).method = "GET"; 
			return this.subscribe(channel,args);
		},
		receive: function(message){
			// summary:
			//		Called when a message is received from the server
			//	message:
			//		A cometd/XHR message
		},
		disconnected: function(){
			// summary:
			// 		called when our channel gets disconnected
			var self = this;
			if(this.connected){ // ifwe are connected, we shall tryto reconnect 
				setTimeout(function(){ // auto reconnect
					self.open();
				},this.autoReconnectTime);
			}
			this.connected = false;
		},
		unsubscribe: function(/*String*/channel, /*dojo.__XhrArgs?*/args){
			// summary:
			// 		unsubscribes from the resource  
			//		See subscribe for parameter values 
			
			args = args || {};
			args.unsubscribe = true;
			this.subscribe(channel,args); // change the time frame to after 5000AD 
		},
		disconnect: function(){
			// summary:
			// 		disconnect from the server  
			this.connected = false;
			this.xhr.abort();
		}
	};
	if(dojox.cometd.connectionTypes){ 
		// register as a dojox.cometd transport and wire everything for cometd handling
		// below are the necessary adaptions for cometd
		Channels.startup = function(data){ // must be able to handle objects or strings
			Channels.open();
			this._cometd._deliver({channel:"/meta/connect",successful:true}); // tell cometd we are connected so it can proceed to send subscriptions, even though we aren't yet 

		};
		Channels.check = function(types, version, xdomain){
			for(var i = 0; i< types.length; i++){
				if(types[i] == "http-channels"){
					return !xdomain;
				}
			}
			return false;
		};
		Channels.deliver = function(message){ 
			// nothing to do
		};
		Channels.receive = function(message){
			this._cometd._deliver(message);
		}
		Channels.sendMessages = function(messages){
			for(var i = 0; i < messages.length; i++){
				var message = messages[i];
				var channel = message.channel;
				var cometd = this._cometd;
				var args = {
					confirmation: function(){ // send a confirmation back to cometd
						cometd._deliver({channel:channel,successful:true});
					}
				};
				if(channel == '/meta/subscribe'){
					this.subscribe(message.subscription,args);
				}else if(channel == '/meta/unsubscribe'){
					this.unsubscribe(message.subscription,args);
				}else if(channel == '/meta/connect'){
					args.confirmation();
				}else if(channel == '/meta/disconnect'){
					Channels.disconnect();
					args.confirmation();
				}else if(channel.substring(0,6) != '/meta/'){
					this.publish(channel,message.data);
				}
			}
		};
		dojox.cometd.connectionTypes.register("http-channels", Channels.check, Channels,false,true);
	}
	if(dojox.rpc.Rest){
		// override the default Rest handler so we can add subscription requests
		var defaultGet = dojox.rpc.Rest._get;
		dojox.rpc.Rest._get = function(){
			// when there is a REST get, we will intercept and add our own xhr handler
			var defaultXhrGet = dojo.xhrGet;  
			dojo.xhrGet = function(r){
				return Channels.get(r.url,r);
			};

			var result = defaultGet.apply(this,arguments);
			dojo.xhrGet = defaultXhrGet;
			return result;
		};
	}
	if(dojox.data && dojox.data.restListener){
		dojo.connect(Channels,"receive",null,dojox.data.restListener);
	}
})();

}
