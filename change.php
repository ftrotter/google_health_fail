<?php

   $xslDoc = new DOMDocument();
   $xslDoc->load("ccr.xsl");

   $xmlDoc = new DOMDocument();
   $xmlDoc->load("ccr_Frederick_Trotter_200902130205.xml");

   $proc = new XSLTProcessor();
   $proc->importStylesheet($xslDoc);
   echo $proc->transformToXML($xmlDoc);
?>
