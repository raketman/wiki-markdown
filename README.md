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

````
docker run  -v $(pwd)/../docs2:/var/www/app/docs -p 74:8000 -e APP_PREFIX=/wiki raketman/wiki

````

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

### Wiki settings ###


#### File settings ####
The file name is taken from the first line, the # symbols at the beginning and at the end are removed

```
# Описание апи #  -> Описание апи

```

#### Dir settings ####

.meta - file for describing folders on yaml format

Supported keys:
```yaml
- title: folder name
- order: # sorting within a folder, for point adjustment you can write values with a minus
    filename15: -10 # will be first
    dirname20: 99999 # will be at the end
    filename7: -7

```

#### Filtering ####
The wiki does not include files that
- start with _
- start with.
- do not contain p.
- are in .html format

The wiki does not include directories that
- start with.
- empty dir on finish filtering


### Search ###


#### Search engine ####
Search work with meilisearch
