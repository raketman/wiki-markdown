### Quick start ###

#### To create wiki page ####

```bash
  docker run -v {wiki_path}:/var/www/app/docs -p {port}:8000 raketman/wiki
```

App will be available on port :{port} in your machine.


#### Force update content ####

```bash
 docker exec -it {container} php bin/console app:wiki:actualize --force=1
```

