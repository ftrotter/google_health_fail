if(!dojo._hasResource["dojox.rpc.JsonRPC"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojox.rpc.JsonRPC"] = true;
dojo.provide("dojox.rpc.JsonRPC");
dojo.require("dojox.rpc.Service");

(function(){
	var jsonRpcEnvelope = {
		serialize: function(smd, method, data, options){
			//not converted to json it self. This  will be done, if
			//appropriate, at the transport level

			var d = dojo.toJson({
				id: this._requestId++,
				method: method.name,
				params: data
			});

			return {
				data: d,
				handleAs:'json',
				contentType: 'application/json',
				transport:"POST"
			};
		},

		deserialize: function(obj){
			if(obj.error){
				var e = new Error(obj.error);
				e._rpcErrorObject = obj.error;
				return e;
			}
			return obj.result || true;
		}
	};
	dojox.rpc.envelopeRegistry.register(
		"JSON-RPC-1.0",
		function(str){
			return str == "JSON-RPC-1.0";
		},
		dojo.mixin({namedParams:false},jsonRpcEnvelope) // 1.0 will only work with ordered params
	);

	dojox.rpc.envelopeRegistry.register(
		"JSON-RPC-2.0",
		function(str){
			return str == "JSON-RPC-2.0";
		},
		jsonRpcEnvelope
	);
})();

}
