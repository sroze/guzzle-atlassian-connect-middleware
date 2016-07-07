adlogix/guzzle-atlassian-connect-middleware
===========================================

![build](https://travis-ci.org/adlogix/guzzle-atlassian-connect-middleware.svg?branch=develop)

__The purpose of this middleware is to implement Atlassian Connect authentication as a Guzzle middleware. So you should read the [Atlassian Connect documentation](https://developer.atlassian.com/static/connect/docs/latest/index.html) to understand terms used in this library__

## tl;dr;
* __JWT__: Json Web Token [standard](http://jwt.io/), [atlassian version](https://developer.atlassian.com/static/connect/docs/latest/concepts/understanding-jwt.html), [Atlassian JWT Web decoder](http://jwt-decoder.herokuapp.com/jwt/decode) (use your query to get the QSH, use the query + the JWT token query param to validate it) 
* __QSH__: [Query String Hash](https://developer.atlassian.com/static/connect/docs/latest/concepts/understanding-jwt.html#qsh)
* __Descriptor__: [Add-on Descriptor for Atlassian Connect](https://developer.atlassian.com/static/connect/docs/latest/modules/), [validate your descriptor against an atlassian product](https://atlassian-connect-validator.herokuapp.com/validate) (syntax check only) 

## What it is
As you may have seen, Atlassian has a pretty complex authentication system, even just for read access on any of their products. We created this Guzzle middleware in order to help us to authenticate on an Atlassian product.

## What it is not
* A full Atlassian \[name-your-product\] client.

## Usage

See the index.php at root of this repository.

## Real life testing

The Atlassian Product you want to authenticate to needs to contact your application using some form of webhooks, so we created the most basic application we could do to show you how it can be accomplished.

Use docker-compose:

```bash
$ docker-compose up -d
```


### Use host names instead of ports

If you've launched the environment you already have a proxy running to redirect atlassian-confluence.dev and atlassian-connect.dev to the correct containers.
You just have to put both domains to your host file or use a solution like [dnsmasq on OSX](https://passingcuriosity.com/2013/dnsmasq-dev-osx/), but be sure to redirect to your docker-machine IP.

To find your docker machine ip use:

```bash
$ docker-machine ip [machine-name]
```


### Proxy Timeout

We've setup a proxy timeout of 10 minutes so we can do line by line without problems ;)
