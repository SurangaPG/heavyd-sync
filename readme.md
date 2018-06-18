# Sync

Small build.xml integration optimised to handle the syncing of all 
the data in the properties to the main data repository.

Install it by placing the following lines in your build.xml

``
    <includepath classpath="${project.basedir}/.heavyd/vendor/surangapg/heavyd-syncc/lib/phing/src" />
```

## Extra

This item has been kept as light as possible to prevent regression issues
etc. This also means it reads all the yaml files in the most direct 
method possible. 
