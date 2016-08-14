# Local Eventing
--------------------------

A simple php trait that allows for execution of methods in an eventing like fashion.  For use when creating a full eventing / listener classes may be a tad too much.

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

## Installation
-------------------------

```
$ composer require josephlavin/local-eventing
```

## Why?
-------------------------

I found myself wanting to use a trait that hooked into an existing systems events, but that system did not broadcast an event that could be listened to.  Using this local eventing I am able to create a simple trait that hooks into a that base system.

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

We can create another trait that relies on the LocalEventing trait and adds `__onLocalEvent__` methods.  Notice how this trait makes an abstract method `_require_trait_LocalEventing`.  This serves as a reminder to the developer that this trait relies on the `LocalEventing` trait to work properly.

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

Now any of my models (that extend base model) can use the `UuidPrimaryKey` trait and have that functionality.

```php
class MyModel extends BaseModel
{
    use uuid;
}
````
