adlogix/guzzle-atlassian-connect
================================
__The purpose of this middleware is to implement Atlassian Connect authentication as a Guzzle middleware. So you should read the [Atlassian Connect documentation](https://developer.atlassian.com/static/connect/docs/latest/index.html) to understand terms used in this library__

## tl;dr;
* __JWT__: Json Web Token [standard](http://jwt.io/), [atlassian version](https://developer.atlassian.com/static/connect/docs/latest/concepts/understanding-jwt.html), [Atlassian JWT Web decoder](http://jwt-decoder.herokuapp.com/jwt/decode) (use your query to get the QSH, use the query + the JWT token query param to validate it) 
* __QSH__: [Query String Hash](https://developer.atlassian.com/static/connect/docs/latest/concepts/understanding-jwt.html#qsh)
* __Descriptor__: [Add-on Descriptor for Atlassian Connect](https://developer.atlassian.com/static/connect/docs/latest/modules/), [validate your descriptor against an atlassian product](https://atlassian-connect-validator.herokuapp.com/validate) (syntax check only) 
