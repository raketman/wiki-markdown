### Quick start ###

#### To create wiki page ####

```bash
  docker run -v {wiki_path}:/var/www/app/docs -p {port}:8000 raketman/wiki
```

Wiki will be available on port :{port} in your machine.

#### Try demo ####

```bash
  git clone https://github.com/raketman/wiki-markdown.git
  docker run -v $(pwd)/wiki-markdown/docs_example:/var/www/app/docs -p 74:8000 raketman/wiki
```

Wiki open on 127.0.0.1:74 or 0.0.0.0:74

#### Proxy pass ####

If need open wiki on part of your app, for example your_app_address/wiki.

You need to add environment APP_PREFIX to docker

```bash
    docker run  -v $(pwd)/../docs2:/var/www/app/docs -p 74:8000 -e APP_PREFIX=/wiki raketman/wiki
```

Of coures all you images url in wiki must be relative path, not absolute, for example
```
    work wine
    ![GitHub Pages](./_images/icon.svg)
    
    work wrong
    ![GitHub Pages](/_images/icon.svg)
```

Need to send wikiapp uri without prefix
You need clean prefix, for example nginx:
```
    location /wiki {
        rewrite /wiki/(.*) /$1  break;
        proxy_pass 0.0.0.0:74
    }
```
in dir docker you can see  docker-compose with example APP_PREFIX


#### Force update content ####

```bash
  docker exec -it {container} php bin/console app:wiki:actualize --force=1
```
