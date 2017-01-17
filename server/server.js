var express = require('express');
var php = require('node-php');
var path = require('path');

var app = express();

const PORT = 8080;

//start listening on port 80
app.listen(PORT, function() {
    console.log('BHSPromApp v3.3');
    console.log('Created by Richard Kriesman for L.D. Bell High School');
    console.log('Licensed under the GPLv3');
    console.log('-----------------------------------------------------');
    console.log('Initialization completed');
    console.log('Started listening on port ' + PORT);
});

//route requests through php-cgi
app.use('/', php.cgi(__dirname + '/../src'));