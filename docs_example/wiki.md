### Wiki settings ###


#### File settings ####
The file name is taken from the first line, the # symbols at the beginning and at the end are removed

```bash
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
