[![Build Status](https://img.shields.io/circleci/build/gh/rs-world/instantiator/main?style=flat-square)](https://circleci.com/gh/rs-world/instantiator/tree/master)
[![Issues](https://img.shields.io/github/issues/rs-world/instantiator?style=flat-square&color=blue)](https://github.com/rs-world/instantiator/issues)
[![Forks](https://img.shields.io/github/forks/rs-world/instantiator?style=flat-square&color=purple)](https://github.com/rs-world/instantiator/network/members)
[![Stars](https://img.shields.io/github/stars/rs-world/instantiator?style=flat-square)](https://github.com/rs-world/instantiator/stargazers)
[![License](https://img.shields.io/github/license/rs-world/instantiator?color=teal&style=flat-square)](https://github.com/rs-world/instantiator/blob/master/LICENSE)


# Instantiator
Instantiator a very light weight small library which implements Instantiator pattern to solve dependency problems between software components or classes. For more about Instantiator pattern, visit [this link](https://github.com/reyadussalahin/instantiator-pattern).


## Getting started
Instantiator is very easy to use and handle dependencies. One of the great properties about is it is very flexible.

### Get Instantiator using composer
`Instantiator` library is not added to packgist yet. So, you need to add this repo to your composer and install it.You can add the following to your composer.json file:

```json
{
    "repositories": [
        {
            "url": "https://github.com/rs-world/instantiator.git",
            "type": "git",
            "no-api": true
        }
    ],
    "require": {
        "rs-world/instantiator": "*"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Okay, once installed you can use it on your project.

### How to use Instantiator
To use instantiator you must extends `Instantiator class`. Look at the example below which creates an `Instantiator` for `Database Class`:

```php
class DatabaseIntantiator extends Instantiator
{
    protected function register()
    {
        $this->instance([
            "default" => function($a, $b) {
                return new \Path\To\Database($a, $b);
            },
            "test" => function($a, $b) {
                return new \Path\To\FakeDatabase($a, $b);
            }
        ]);
    }

    public function get(A $a, B $b): \Path\To\DatabaseInterface
    {
        return $this->getInstance($a, $b);
    }
}
```

Now, you can use DatabaseInstantiator class as follows:

```php
// in default mode
$dbi = new DatabaseInstantiator();
$db = $dbi->get($a, $b);
var_dump($db instanceof \Path\To\Database); // prints true

// in test mode
$dbi = new DatabaseInstantiator("test", true);
$db = $dbi->get($a, $b);
var_dump($db instanceof \Path\To\DatabaseFake); // prints true
```

You can set `Instantiator`'s mode to `test` globally. To do that:
```php
// set global mode to "test"
Instantiator::setGlobalMode("test");
// now
$dbi = new DatabaseInstantiator();
$db = $dbi->get($a, $b);
var_dump($db instanceof \Path\To\Database); // prints false
var_dump($db instanceof \Path\To\DatabaseFake); // prints true
```

### Resolving dependencies using Instantiator
You can easily handle dependency using Instantiator. Let there be a Service class which depends on Database. Then,

```php
class ServiceInstantiator extends Instantator
{
    protected function register()
    {
        // ...
        // ...
        $dbi = new \Path\To\DatabaseInstantiator(
            $this->getMode(),
            $this->getFallback()
        );
        $db = $dbi->getInstance($a, $b);

        $this->instance([
            "default" => function($x) use($db) {
                return \Path\To\Service($db, $x);
            }
        ]);
    }

    public function get($x): ServiceInterface
    {
        return $this->getInstance($x);
    }
}
```

You can use it as follows:

```php
// ...
$si = new ServiceInstantiator();
$service = $si->get($x);
// ...
```
And you don't have to worry about database instantiating, and also you don't have to worry about which databse class to use when it is testing time, cause "DatabaseFake" will be instantiating when mode is set to "test". It makes your task that simple!
  
Instantiator brings much more functionality to table, whether you're in production or testing or developing applicatons. The documentation of Instantiator is under process, and any type of contribution is very much welcome.


## LICENSE
To learn about the project license, visit [here](https://github.com/rs-world/instantiator/blob/master/LICENSE).


## Contributing
The project is ongoing and it has a lot of potential to grow. So, if you've any ideas or improvements, send a pull request. I'll have a look as soon as possible and provide feedback.
