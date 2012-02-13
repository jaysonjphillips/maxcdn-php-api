#README

Copyright (c) 2011 Jayson J. Phillips, Chronium Labs LLC

##VERSION 1.0.1

##ABOUT MAXCDN-API 
This project is a PHP wrapper that allows a programatic interface to [MaxCDN's](http://wiki.netdna.com/Glossary/API_Documentation "MaxCDN API Docs") XML-RPC API. This library only has one dependency, the open source XMLRPC for PHP library (specifically, lib folder). The aim of this class is to be a lightweight drop-in for use in any project or framework. It is licensed under the MIT License, the text of which is at the bottom of this document.


##QUICK START

Example: Get Account Bandwidth  
Returns an _xmlrpcresp_ object  

	$maxcdn = new MaxCDN('api-key-goes-here', 'user-id-here');
	$result = $maxcdn->getBandwidth($from_date, $to_date);

From here, you can simply use xmlrpc for php methods on the returned object  

	if(!$result->faultCode()) {
		$value = $result->value();
	}

	print $value->scalarval();

As you can see, no need to fuss around with dropping in namespaces, method names, ISO formatted dates or the like. 
Two lines and you're ready to cook. _Simple_

##CONTRIBUTORS
Thanks to @philsturgeon for the timezone heads-up and his [CodeIgniter](https://github.com/philsturgeon/codeigniter-maxcdn) port/mod of this class.

## LICENSE

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

_The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software._

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.