# AdadgioGearBundle

# Installation

If you want to use NodeRed connectors, loops and flows

```
// import routing
_adadgio_gear:
    resource: "@AdadgioGearBundle/Resources/config/routing.yml"
```

```
// in config.yml


```

## CSV reader

```php
use Adadgio\GearBundle\Component\Reader\Csv;

$csv = new Csv('data/test.csv');

$data = $csv
    ->setDelimiter(';')
    ->read(5, 15) // reads rows from 5 to 15 included (pass null for no limit and offset)
    ->getData();
```

## Entity hydration from data
<sub>
```php
use Adadgio\GearBundle\Component\Hydration\EntityHydrator;

$hydrator = new EntityHydrator();

// $data = ... data from the previous example
$hydrator
    ->hydrate('Adadgio\DoctrineDQLBundle\Entity\TestEntity')
    ->with($data)
    ->map(0, 'id') // map array column index to entity property
    ->map(1, 'name');

$entities = $hydrator->getEntities();
```
</sub>
