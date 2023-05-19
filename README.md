# CustomQueryBuilder

### Principe

Le but est de proposer un QueryBuilder personnalisé.
Celui-ci s'occupe de formater la requête SQL et retourne une chaine de caractère qui peut être intégré dans n'importe quel base de donnée comprennant le SQL.


### Exemple
#### SELECT
```
$query = (new QueryBuilder())
    ->select('*')
    ->from('table')
    ->where('name = :name AND description = :description')
    ->addArgument('name', 'hello')
    ->addArgument('description', 'world');
```
#### INSERT INTO
```
$query = (new QueryBuilder())
    ->insertInto('table')
    ->inFields("name", "description")
    ->withValues($name, $description);
```

Le fait d'insérer l'objet ```$query``` en tant que chaine de caractère va automatiquement transformer l'objet en chaine de caractère lisible.

### Dépendance PHP (dev)

La bibliothèque sqlite.(dll|so) est nécessaire pour exécuter les tests