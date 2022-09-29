# blog-api

API symfony
composer require jms/serializer-bundle
php bin/console doctrine:database:create && php bin/console doctrine:schema:create

composer require friendsofsymfony/rest-bundle
voir la configuration du bundle
php bin/console config:dump-reference fos_rest
config se trouve ici
config\packages\fos_rest.yaml

#pagination du resultat
composer require babdev/pagerfanta-bundle

#API autodecouvrable RESTful
composer require willdurand/hateoas-bundle
