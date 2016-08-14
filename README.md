# Local Eventing

A simple php trait that allows for execution of local methods in an eventing like fashion.  Add mehods to a class using this trait following a naming convention and they will be executed when the local event is fired.

```php
use josephlavin\localEventing\LocalEventing;

class dummy
{
    use LocalEventing;
    
    public function save()
    {
        // do your saving logic...
        $this->fireLocalEvent('saved');
    }

    protected function __onLocalEvent_saved_send_notification()
    {
        // Logic here to send notification...
    }

    protected function __onLocalEvent_saved_log_event()
    {
        // Logic for logging here...
    }
}
```

## Event Listener Method Naming Convention

All event listener methods must be named in this fashion:
`__onLocalEvent_[event]_[description]()`

- `__onLocalEvent_` : namespace for the local eventing system
- `[event]`: the same string given to `$this->fireLocalEvent('event')`
- `[description]`: a simple description of what this method does


## Installation

```
$ composer require josephlavin/local-eventing
```

## Use Case

Lets say we have a basic model type class which fires local events:

```php
class BaseModel
{
    use LocalEventing;

    public function __construct()
    {
        $this->fireLocalEvent('created');
    }

    public function insert()
    {
        $this->fireLocalEvent('preInsert');
        // the insert logic...
        $this->fireLocalEvent('postInsert');
    }
}
```

We can create another trait that relies on the LocalEventing trait and adds `__onLocalEvent__` methods.  Notice how this trait has an abstract method `_require_trait_LocalEventing`.  This serves as a reminder to the developer that this trait relies on the `LocalEventing` trait to work properly.

```php
trait UuidPrimaryKey
{
    // reminder that we must also use LocalEventing Trait
    abstract protected function _require_trait_LocalEventing();

    protected function __onLocalEvent_preInsert_populate_uuid()
    {
        // generate and set a uuid primary key here...
    }
}
```

Now any models (that extend base model) can use the `UuidPrimaryKey` trait and gain that functionality.

```php
class MyModel extends BaseModel
{
    use UuidPrimaryKey;
}
````
