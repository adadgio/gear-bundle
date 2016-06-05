# AdadgioGearBundle

# Installation

## Api annotations and auth

Its very easy to create API endpoints and secure them through any kind of authentication system.

### Configuration

```yaml
// in config.yml (basic auth example)
adadgio_gear:
    auth:
        type: Basic     # options: Basic (more default types not available in current version)
        class: ~        # either define "class" or "provider", ex. "Adadgio\GearBundle\Component\Api\Authenticatir\AuthProvider"
        #provider: ~    # either define "class" or "provider", ex. "adadgio_gear.api.authenticator_example_service"
        user: benny
        password: test

// in config.yml (custom service auth example, like API client in database)
adadgio_gear:
    auth:
        #type: ~        
        #class: ~       # either define "class" or "provider", ex. "Adadgio\GearBundle\Component\Api\Authenticatir\AuthProvider"
        provider: my_bundle.api.my_client_auth  # you create the service and define what to do: see "adadgio_gear.api.authenticator_example_service"
```

Example using custom authenticator service.

```php
use Adadgio\GearBundle\Component\Api\Authenticator\AuthProvider;
use Adadgio\GearBundle\Component\Api\Authenticator\AuthProviderInterface;

class ExampleAuthProviderService extends AuthProvider implements AuthProviderInterface
{
    /**
     * Build your service like you build services every day!
     */
    public function __construct()
    {
        // inject anything in here, like doctrien.orm.entity_manager, or whatever.
    }

    /**
     * Checks auth. You could get request headers key and check that
     * the secret key and client id are in your database for example...
     *
     * @return boolean
     */
    public function authenticate()
    {
        // your owns logic here
        $request = $this->getRequest();
        $headers = $request->getHeaders();

        return true;
    }
}
```

## NodeRed connector(s) and loops

### Configuration

```yaml
// import routing
_adadgio_gear:
    resource: "@AdadgioGearBundle/Resources/config/routing.yml"
```

```yaml
// in config.yml
adadgio_gear:
    nodered:
        host: 127.0.0.1
        port: 1880          # optional
        protocol: http://   # optional
        http_auth:          # optional (depends on Node Red httpNodeAuth param)
            user: ~ 
            pass: ~

```

Then you need to install the flows in your NodeRed app.

```bash
$ php app/console adadgio:nodered:install --output=/destination/folder
```

You will need to manually import the flows in your NodeRed app (or use flows directory config in NodeRed settings.js).

![alt tag](https://raw.githubusercontent.com/adadgio/gear-bundle/master/Resources/help/nodered_flow.png)

### Usage

To trigger a loop (or just a delayed message), you need to create a `\Payload` that node red will send back to the AdagagioGearBundle loop controller (see `routing.yml` for more info). The controller dispatched an event when it receives back the payload. You can **listen to the event** and modify the payload to achieve your goal.

```php
use Adadgio\GearBundle\Connector\NodeRed\Payload;

// payload contains 3 initial params you cannot override (pid, kill, iteration)
// and they change automatically during the loop lifecycle
$payload = new Payload();

// add more params
$payload->setParameter('my_name', 'Romain'); // nb: 3 params are here by default

// use the connector to start (trigger) the loop
$this->get('adadgio_gear.nodered.connector')->send('POST', '/adadgio/gear/loop/start', $payload); // @todo pass this more transparently
```

The loop will never stop until you change the payload **kill** property. Now **listen** to the loop callbacks. Nodered will indefinitaly call it unless you `kill` the payload.

```php
// in some listener, far, far away, a long long time ago
// the listener must listen to "adadgio_gear.nodered.payload_received"
public function onPayloadReceived(\Adadgio\GearBundle\Connector\NodeRed\Event\PayloadEvent $event)
{
    // you might need the request, who knows
    $request = $event->getRequest();

    $payload = $event->Payload();
    // notice iteration changed to +1, pid always stays the same (unless you trigger another process)
    // otherwise you get back the parameters you defined earlier

    // if you wanna stop the flow
    if ($payload->getIteration() > 3) {
        $payload->kill();
    }

    // process... something, or modify your input params at runtime
    $name = $payload->getParameter('my_name');
    $name = ... // change your name!
    $payload->setParameter('my_name', $name);
}
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
