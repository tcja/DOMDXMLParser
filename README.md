# Menu

* [How to install](https://github.com/tcja/DOMDXMLParser#user-content-how-to-install)
* [Read, check and compare data methods](https://github.com/tcja/DOMDXMLParser#user-content-read-check-and-compare-data-methods)
* [Edit, add and remove data methods](https://github.com/tcja/DOMDXMLParser#user-content-edit-add-and-remove-data-methods)
* [Requirements](https://github.com/tcja/DOMDXMLParser#user-content-requirements)
* [Dependencies](https://github.com/tcja/DOMDXMLParser#user-content-dependencies)
* [License](https://github.com/tcja/DOMDXMLParser#user-content-license)
* [Changelog](https://github.com/tcja/DOMDXMLParser#user-content-changelog)

# DOMDXMLParser

DOMDXMLParser is a class that can handle multiple CRUD tasks with XML files

## This class currently only supports the following XML layout examples

Simple layout with one level node along with attributes (default layout) :
```xml
<?xml version="1.0" encoding="UTF-8"?>
<accounts>
  <account id="1" type="supreme admin" email="supreme-admin@mail.com" name="Supreme Admin" password="$2y$12$BsNUcdEIoN2GeFLucqmfbuiGOWzHyCfxpOczswgMZXqrsmYZx363O"><![CDATA[Supreme admin account]]></account>
  <account id="2" type="admin" email="first-admin@mail.com" name="First Admin" password="$2y$12$jZGZuXODSvBXTQXtvguHTOnXxdFgamQvWumSBYQ11bkCWR/tG5ZIu"><![CDATA[Admin account]]></account>
  <account id="3" type="admin" email="second-admin@mail.com" name="Second Admin" password="$2y$12$pCPyVWZWAtXNLYBoCKTlY.pxZhEJEq6Rf8JV0eDsjo6sArkzTYyqi"><![CDATA[Admin account]]></account>
  <account id="4" type="user" email="first-user@mail.com" name="First User" password="$2y$12$ZoNNlEc9LjXjBynueyqljO5pai.ZVz4RjLZxMdLxBijwwRd5H70OS"><![CDATA[User account]]></account>
  <account id="5" type="user" email="second-user@mail.com" name="Second User" password="$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a"><![CDATA[User account]]></account>
  <account id="6" type="user" email="third-user@mail.com" name="Third User" password="$2y$12$pGQw1CMCryJu5j0FcPVqiORo3/g.Fkv78TaDAcy5m68UO//L8XQYS"><![CDATA[User account]]></account>
</accounts>
```
or a layout with 2 levels nodes comprising each data in a single node without any attributes :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<accounts>
  <account>
    <id>1</id>
    <type>supreme admin</type>
    <email>supreme-admin@mail.com</email>
    <name>Supreme Admin</name>
    <password>$2y$12$BsNUcdEIoN2GeFLucqmfbuiGOWzHyCfxpOczswgMZXqrsmYZx363O</password>
    <description>Supreme admin account</description>
  </account>
  <account>
    <id>2</id>
    <type>admin</type>
    <email>first-admin@mail.com</email>
    <name>First Admin</name>
    <password>$2y$12$jZGZuXODSvBXTQXtvguHTOnXxdFgamQvWumSBYQ11bkCWR/tG5ZIu</password>
    <description>Admin account</description>
  </account>
  <account>
    <id>3</id>
    <type>admin</type>
    <email>second-admin@mail.com</email>
    <name>Second Admin</name>
    <password>$2y$12$pCPyVWZWAtXNLYBoCKTlY.pxZhEJEq6Rf8JV0eDsjo6sArkzTYyqi</password>
    <description>Admin account</description>
  </account>
  <account>
    <id>4</id>
    <type>user</type>
    <email>first-user@mail.com</email>
    <name>First User</name>
    <password>$2y$12$ZoNNlEc9LjXjBynueyqljO5pai.ZVz4RjLZxMdLxBijwwRd5H70OS</password>
    <description>User account</description>
  </account>
  <account>
    <id>5</id>
    <type>user</type>
    <email>second-user@mail.com</email>
    <name>Second User</name>
    <password>$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a</password>
    <description>User account</description>
  </account>
  <account>
    <id>6</id>
    <type>user</type>
    <email>third-user@mail.com</email>
    <name>Third User</name>
    <password>$2y$12$pGQw1CMCryJu5j0FcPVqiORo3/g.Fkv78TaDAcy5m68UO//L8XQYS</password>
    <description>User account</description>
  </account>
</accounts>
```
## How to install

Using composer : `composer require tcja/domdxmlparser`

or just download the class itself and install it manually

## Read, check and compare data methods

**Check a node existance by its value or by its attribute/value pair :** suppose we want to check a user's e-mail existence from an `email` attribute :
```php
// Since DOMDXMLParser uses method names such as sortBy() and toArray(),
// we use the class in its own namespace to avoid conflict with same methods
// names within famous frameworks like laravel or symfony
use Tcja\DOMDXMLParser;

$xml = new DOMDXMLParser('path/to/xml/file');
$check = $xml->checkNode('email', 'second-user@mail.com');
var_dump($check); // Output : true if email was found, false if not
```
to check that same e-mail using the non-attribute layout style :
```php
$check = $xml->checkNode('second-user@mail.com');
```

**Collect data from a specific node and have them stored in an array :** suppose we want to get all data from the user with the e-mail `second-user@mail.com` : 

```php
$data = $xml->pickNode('email', 'second-user@mail.com')->fetchData()->toArray();
var_dump($data);
```
Output :

```php
array(6) {
  ["id"]=>
  string(1) "5"
  ["type"]=>
  string(4) "user"
  ["email"]=>
  string(20) "second-user@mail.com"
  ["name"]=>
  string(11) "Second User"
  ["password"]=>
  string(60) "$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a"
  ["nodeValue"]=>
  string(12) "User account"
}
```
To do that according to the non-attribute layout style, just select the node by its value :
```php
$data = $xml->pickNode('second-user@mail.com')->fetchData()->toArray();
```

**Get an attribute value from a specific node :**
```php
$value = $xml->pickNode('email', 'second-user@mail.com')->getAttr('name');
var_dump($value); // Output : "Second User";
```
To do that according to the non-attribute layout style, pass the tag name as an argument to `getValue()` method : 
```php
$value = $xml->pickNode('second-user@mail.com')->getValue('name');
```

**Get a node value :**
```php
$value = $xml->pickNode('email', 'second-user@mail.com')->getValue();
var_dump($value); // Output : "User account";
```
To do that according to the non-attribute layout style, pass the tag name as an argument to `getValue()` method : 
```php
$value = $xml->pickNode('second-user@mail.com')->getValue('description');
```

**Get all nodes data :**
```php
$data = $xml->pickNode('account')->fetchData()->toArray();
var_dump($data);
```
Output :
```php
array(6) {
  [0]=>
  array(6) {
    ["id"]=>
    string(1) "1"
    ["type"]=>
    string(13) "supreme admin"
    ["email"]=>
    string(22) "supreme-admin@mail.com"
    ["name"]=>
    string(13) "Supreme Admin"
    ["password"]=>
    string(60) "$2y$12$BsNUcdEIoN2GeFLucqmfbuiGOWzHyCfxpOczswgMZXqrsmYZx363O"
    ["nodeValue"]=>
    string(21) "Supreme admin account"
  }
  [1]=>
  array(6) {
    ["id"]=>
    string(1) "2"
    ["type"]=>
    string(5) "admin"
    ["email"]=>
    string(20) "first-admin@mail.com"
    ["name"]=>
    string(11) "First Admin"
    ["password"]=>
    string(60) "$2y$12$jZGZuXODSvBXTQXtvguHTOnXxdFgamQvWumSBYQ11bkCWR/tG5ZIu"
    ["nodeValue"]=>
    string(13) "Admin account"
  }
  [2]=>
  array(6) {
    ["id"]=>
    string(1) "3"
    ["type"]=>
    string(5) "admin"
    ["email"]=>
    string(21) "second-admin@mail.com"
    ["name"]=>
    string(12) "Second Admin"
    ["password"]=>
    string(60) "$2y$12$pCPyVWZWAtXNLYBoCKTlY.pxZhEJEq6Rf8JV0eDsjo6sArkzTYyqi"
    ["nodeValue"]=>
    string(13) "Admin account"
  }
  [3]=>
  array(6) {
    ["id"]=>
    string(1) "4"
    ["type"]=>
    string(4) "user"
    ["email"]=>
    string(19) "first-user@mail.com"
    ["name"]=>
    string(10) "First User"
    ["password"]=>
    string(60) "$2y$12$ZoNNlEc9LjXjBynueyqljO5pai.ZVz4RjLZxMdLxBijwwRd5H70OS"
    ["nodeValue"]=>
    string(12) "User account"
  }
  [4]=>
  array(6) {
    ["id"]=>
    string(1) "5"
    ["type"]=>
    string(4) "user"
    ["email"]=>
    string(20) "second-user@mail.com"
    ["name"]=>
    string(11) "Second User"
    ["password"]=>
    string(60) "$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a"
    ["nodeValue"]=>
    string(12) "User account"
  }
  [5]=>
  array(6) {
    ["id"]=>
    string(1) "6"
    ["type"]=>
    string(4) "user"
    ["email"]=>
    string(19) "third-user@mail.com"
    ["name"]=>
    string(10) "Third User"
    ["password"]=>
    string(60) "$2y$12$pGQw1CMCryJu5j0FcPVqiORo3/g.Fkv78TaDAcy5m68UO//L8XQYS"
    ["nodeValue"]=>
    string(12) "User account"
  }
}
```

**Sort the results by a specific attribute :** suppose we want to sort the previous data by email in ascending order :
```php
$data = $xml->pickNode('account')->fetchData()->sortBy('email')->toArray();
var_dump($data);
```
Output :
```php
array(6) {
  [0]=>
  array(6) {
    ["id"]=>
    string(1) "2"
    ["type"]=>
    string(5) "admin"
    ["email"]=>
    string(20) "first-admin@mail.com"
    ["name"]=>
    string(11) "First Admin"
    ["password"]=>
    string(60) "$2y$12$jZGZuXODSvBXTQXtvguHTOnXxdFgamQvWumSBYQ11bkCWR/tG5ZIu"
    ["nodeValue"]=>
    string(13) "Admin account"
  }
  [1]=>
  array(6) {
    ["id"]=>
    string(1) "4"
    ["type"]=>
    string(4) "user"
    ["email"]=>
    string(19) "first-user@mail.com"
    ["name"]=>
    string(10) "First User"
    ["password"]=>
    string(60) "$2y$12$ZoNNlEc9LjXjBynueyqljO5pai.ZVz4RjLZxMdLxBijwwRd5H70OS"
    ["nodeValue"]=>
    string(12) "User account"
  }
  [2]=>
  array(6) {
    ["id"]=>
    string(1) "3"
    ["type"]=>
    string(5) "admin"
    ["email"]=>
    string(21) "second-admin@mail.com"
    ["name"]=>
    string(12) "Second Admin"
    ["password"]=>
    string(60) "$2y$12$pCPyVWZWAtXNLYBoCKTlY.pxZhEJEq6Rf8JV0eDsjo6sArkzTYyqi"
    ["nodeValue"]=>
    string(13) "Admin account"
  }
  [3]=>
  array(6) {
    ["id"]=>
    string(1) "5"
    ["type"]=>
    string(4) "user"
    ["email"]=>
    string(20) "second-user@mail.com"
    ["name"]=>
    string(11) "Second User"
    ["password"]=>
    string(60) "$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a"
    ["nodeValue"]=>
    string(12) "User account"
  }
  [4]=>
  array(6) {
    ["id"]=>
    string(1) "1"
    ["type"]=>
    string(13) "supreme admin"
    ["email"]=>
    string(22) "supreme-admin@mail.com"
    ["name"]=>
    string(13) "Supreme Admin"
    ["password"]=>
    string(60) "$2y$12$BsNUcdEIoN2GeFLucqmfbuiGOWzHyCfxpOczswgMZXqrsmYZx363O"
    ["nodeValue"]=>
    string(21) "Supreme admin account"
  }
  [5]=>
  array(6) {
    ["id"]=>
    string(1) "6"
    ["type"]=>
    string(4) "user"
    ["email"]=>
    string(19) "third-user@mail.com"
    ["name"]=>
    string(10) "Third User"
    ["password"]=>
    string(60) "$2y$12$pGQw1CMCryJu5j0FcPVqiORo3/g.Fkv78TaDAcy5m68UO//L8XQYS"
    ["nodeValue"]=>
    string(12) "User account"
  }
}
```
To sort in descending order, pass `DESC` value to `sortBy()` second attribute like so :
```php
$data = $xml->pickNode('account')->fetchData()->sortBy('email', 'DESC')->toArray();
```
***Please note that `sortBy()` method also sorts numerical values.***

**Get a specific attribute value from all nodes :** suppose we want to get all users e-mail :
```php
$data = $xml->pickNode('account')->fetchData('email')->toArray();
var_dump($data);
```
Output :
```php
array(6) {
  [0]=>
  string(22) "supreme-admin@mail.com"
  [1]=>
  string(20) "first-admin@mail.com"
  [2]=>
  string(21) "second-admin@mail.com"
  [3]=>
  string(19) "first-user@mail.com"
  [4]=>
  string(20) "second-user@mail.com"
  [5]=>
  string(19) "third-user@mail.com"
}
```

**Get data from all nodes which match a specific attribute value :** suppose we want to get data from the users that have an admin `type` account :
```php
$data = $xml->pickNode('type', 'admin')->fetchData()->toArray();
var_dump($data);
```
Output :
```php
array(2) {
  [0]=>
  array(6) {
    ["id"]=>
    string(1) "2"
    ["type"]=>
    string(5) "admin"
    ["email"]=>
    string(20) "first-admin@mail.com"
    ["name"]=>
    string(11) "First Admin"
    ["password"]=>
    string(60) "$2y$12$jZGZuXODSvBXTQXtvguHTOnXxdFgamQvWumSBYQ11bkCWR/tG5ZIu"
    ["nodeValue"]=>
    string(13) "Admin account"
  }
  [1]=>
  array(6) {
    ["id"]=>
    string(1) "3"
    ["type"]=>
    string(5) "admin"
    ["email"]=>
    string(21) "second-admin@mail.com"
    ["name"]=>
    string(12) "Second Admin"
    ["password"]=>
    string(60) "$2y$12$pCPyVWZWAtXNLYBoCKTlY.pxZhEJEq6Rf8JV0eDsjo6sArkzTYyqi"
    ["nodeValue"]=>
    string(13) "Admin account"
  }
}
```

**Compare an attribute value with the previous one in the same node :** suppose we want to check if the password input from a login form corresponds to the password stored in the XML file :
```php
$checkPassword = $xml->pickNode('email', 'second-user@mail.com')->compareTo('password', '$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a');
vard_dump($checkPassword); // Output : true
```

**Get the highest value amongst all nodes :** suppose we want to get the user with the highest ID :
```php
$highest_user_id = $xml->pickNode('account')->getHighestValue('id');
vard_dump($highest_user_id); // Output : 6
```

**Get the total amount of items from the DOM :**
```php
$total_items = $xml->getTotalItems();
vard_dump($total_items); // Output : 6
```


## Edit, add and remove data methods

**Add a new node in the XML file :** suppose we want to add a new user, first we pass the name of the node and then we can pass an array of attributes and values :
```php
$xml->addNode('account', [
    'id' => 7, 
    'type' => 'user', 
    'email' => 'fourth-user@mail.com', 
    'name' => 'Fourth User', 
    'password' => '$2y$12$WOjspiqT7ZuuMMPGCeZJjuU4hguSpEV9TQSfnjZudGgF9PYj7OAEa', 
    'CDATA' => 'User account' // this will create a CDATA node value, if you just want pure text, use 'textNode' => 'value' instead
]);
```
If using the non-attribute layout style, by default the nodes values will be written in pure text, if we want to use CDATA instead, we just have to set `true` to `addNode()`'s third argument like so :
```php
$xml->addNode('account', [
    'id' => 7, 
    'type' => 'user', 
    'email' => 'fourth-user@mail.com', 
    'name' => 'Fourth User', 
    'password' => '$2y$12$WOjspiqT7ZuuMMPGCeZJjuU4hguSpEV9TQSfnjZudGgF9PYj7OAEa', 
    'description' => 'User account'
], true);
```
If there is no data in the XML file yet and you want to add a node using the non-attribute layout style, please use the `setLayoutStyleNoAttributes()` method and pass `true` to its attribute before using `addNode()` method like so :
```php
$xml->setLayoutStyleNoAttributes(true);
$xml->addNode('account', [
    'id' => 7, 
    'type' => 'user', 
    'email' => 'fourth-user@mail.com', 
    'name' => 'Fourth User', 
    'password' => '$2y$12$WOjspiqT7ZuuMMPGCeZJjuU4hguSpEV9TQSfnjZudGgF9PYj7OAEa', 
    'description' => 'User account'
]);
```
If there is already data in the XML file, we don't need to specify the layout style as the class will guess it for us and add the data accordingly.

**Change node values :** suppose we want to change some data for user with ID "3" (change its email and its user type to "user") :
```php
$xml->pickNode('id', 3)->changeData([
    'type' => 'user', 
    'email' => 'foo-bar@mail.com'
]);
```
To change only one attribute value, we can just pass the attribute as a the first argument and its value to the second one :
```php
$xml->pickNode('id', 3)->changeData('email', 'foo-bar@mail.com');
```

**Add a new attribute :** suppose we want to add a new attribute/value to that user, just pass the new attribute and value as you would do to change one like shown above : 
```php
$xml->pickNode('id', 3)->changeData('newAttribute', 'New value');
```

**Remove an attribute :** suppose we want to remove the newly created attribute above, we would just pass `false` as the second argument to `changeData()` method from the targeted attribute like so : 
```php
$xml->pickNode('id', 3)->changeData('newAttribute', false);
```

**Change specific attributes to all nodes :** suppose we want to reset all users name to "NAME RESET", we pick `account` node and then pass an array with the new value to `changeData()` method : 
```php
$xml->pickNode('account')->changeData(['name' => 'NAME RESET']);
```

**Set new node value :** suppose we want to set a new node value using CDATA to the user with the name "Third User", use `setValue()` method : 
```php
$xml->pickNode('name', 'Third User')->setValue('New node value');
```
to do it using pure text, use `setTextValue()` method instead :
```php
$xml->pickNode('name', 'Third User')->setTextValue('New node value');
```

**Remove a node from the XML file :** suppose the user with the e-mail "second-user@mail.com" wants to remove his account :
```php
$xml->pickNode('email', 'second-user@mail.com')->remove();
```
## Requirements

PHP 7.1 or above

PSR-4 autoload if using composer

## Dependencies

[PHP-Unit](https://phpunit.de/) for unit testing

## License

Released under the **MIT License**

## Changelog

*v1.2* :

Added a new way to remove an attribute, renamed a method and added unit testing :

 - Renamed `DOMDXMLParser::setLayoutStyle($style)` to `DOMDXMLParser::setLayoutStyleNoAttributes($style)` for better readability
 - Remove an attribute by passing `false` to the second argument of `DOMDXMLParser::changData()` method like so : `$xml->pickNode('id', 3)->changeData('newAttribute', false);`
 - Added unit testing **for the default layout style**, requires PHP Unit to perform them

*v1.1.1* :

Fixed minor bugs (`DOMDXMLParser::getTotalItems()` method wasn't calculating items correctly), improved code, added a new method : 

- `DOMDXMLParser::checkLayoutStyle()` :  Checks the current DOM layout style

*v1.1* :

Fixed some bugs, improved some code, also added three new methods : 

- `DOMDXMLParser::setLayoutStyle($style)` :  changes layout style node to single node -> value pair if set to true

- `DOMDXMLParser::getHighestValue($selector)` : shows the highest value of `$selector` amongst all the nodes 

- `DOMDXMLParser::getTotalItems()` : shows the total number of items in the DOM
