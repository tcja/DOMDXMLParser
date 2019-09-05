<?php

require __DIR__ . "/../../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Tcja\DOMDXMLParser;

class DOMDXMLParserTest extends TestCase
{
    private $DOM;

    private $testFile = 'test.xml';

    /**
     * @before
     */
    public function initializeDOMAndTestFile()
    {
        if (!file_exists($this->testFile)) {
            $data = '<?xml version="1.0" encoding="UTF-8"?><accounts><account id="1" type="supreme admin" email="supreme-admin@mail.com" name="Supreme Admin" password="$2y$12$BsNUcdEIoN2GeFLucqmfbuiGOWzHyCfxpOczswgMZXqrsmYZx363O"><![CDATA[Supreme admin account]]></account><account id="2" type="admin" email="first-admin@mail.com" name="First Admin" password="$2y$12$jZGZuXODSvBXTQXtvguHTOnXxdFgamQvWumSBYQ11bkCWR/tG5ZIu"><![CDATA[Admin account]]></account><account id="3" type="admin" name="Second Admin" password="$2y$12$pCPyVWZWAtXNLYBoCKTlY.pxZhEJEq6Rf8JV0eDsjo6sArkzTYyqi" newAttribute="New value" email="second-admin@mail.com"><![CDATA[Admin account]]></account><account id="4" type="user" email="first-user@mail.com" name="First User" password="$2y$12$ZoNNlEc9LjXjBynueyqljO5pai.ZVz4RjLZxMdLxBijwwRd5H70OS"><![CDATA[User account]]></account><account id="5" type="user" email="second-user@mail.com" name="Second User" password="$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a"><![CDATA[User account]]></account><account id="6" type="user" email="third-user@mail.com" name="Third User" password="$2y$12$pGQw1CMCryJu5j0FcPVqiORo3/g.Fkv78TaDAcy5m68UO//L8XQYS"><![CDATA[User account]]></account><account id="7" type="user" email="fourth-user@mail.com" name="Fourth User" password="$2y$12$bAc28XSewFgv9Ih9K9RTCeFRX3IwV9sRuipDFe8KJ1.I9reU42ZOq"/></accounts>';
            file_put_contents($this->testFile, $data);
        }        
        $this->DOM = new DOMDXMLParser($this->testFile);
    }
    /**
     * @after
     */
    public function destroyDOMAndTestFile()
    {
        unlink($this->testFile);      
        $this->DOM = '';
    }

    public function testNodeExistance()
    {
        $check = $this->DOM->checkNode('email', 'second-user@mail.com');
        $this->assertTrue($check);
    }

    public function testNodeNonExistance()
    {
        $check = $this->DOM->checkNode('email', 'random@mail.com');
        $this->assertFalse($check);
    }

    public function testArrayConversion()
    {
        $data = $this->DOM->pickNode('account')->fetchData()->toArray();
        $data2 = $this->DOM->pickNode('account')->fetchData('email')->toArray();
        $data3 = $this->DOM->pickNode('email', 'second-user@mail.com')->fetchData()->toArray();
        $this->assertIsArray($data);
        $this->assertIsArray($data2);
        $this->assertIsArray($data3);
    }

    public function testAttribueValueExistance()
    {
        $value = $this->DOM->pickNode('email', 'second-user@mail.com')->getAttr('name');
        $this->assertIsString($value);
    }

    public function testAttributeValueNonExistance()
    {
        $value = $this->DOM->pickNode('email', 'second-user@mail.com')->getAttr('random-attribute');
        $this->assertEquals('', $value);
    }

    public function testNodeValueExistance()
    {
        $value = $this->DOM->pickNode('email', 'second-user@mail.com')->getValue();
        $this->assertIsString($value);
    }

    public function testNodeValueNonExistance()
    {
        $value = $this->DOM->pickNode('email', 'fourth-user@mail.com')->getValue();
        $this->assertEquals('', $value);
    }

    public function testAttributeValueComparison()
    {
        $value = $this->DOM->pickNode('email', 'second-user@mail.com')->compareTo('password', '$2y$12$zXk23k.usjDgjbG6yJAKtO9EohFFAwnMOzsY3CZsKrgonz3/kh97a');
        $this->assertTrue($value);
    }

    public function testAttributeValueFailedComparison()
    {
        $value = $this->DOM->pickNode('email', 'second-user@mail.com')->compareTo('password', 'randompa$$word');
        $this->assertFalse($value);
    }

    public function testTotalItemsIsNumeric()
    {
        $total_items = $this->DOM->getTotalItems();
        $this->assertIsNumeric($total_items);
    }

    public function testHighestValueIsString()
    {
        $total_items = $this->DOM->pickNode('account')->getHighestValue('id');
        $this->assertIsString($total_items);
    }

    public function testAddNode()
    {
        $this->DOM->addNode('account', [
            'id' => 1337, 
            'type' => 'user', 
            'email' => 'test-user@mail.com', 
            'name' => 'Test User', 
            'password' => '$2y$12$Oe7cym6HgZx.ovXJ13rgUO666cp323dO89//YlQJACLFHfTGRuB1m', 
            'CDATA' => 'User account'
        ]);
        $check = $this->DOM->checkNode('email', 'test-user@mail.com');
        $this->assertTrue($check);
    }

    public function testAddNewAttribute()
    {
        $this->DOM->pickNode('id', 3)->changeData('newAttribute', 'New value');
        $check = $this->DOM->checkNode('newAttribute', 'New value');
        $this->assertTrue($check);
    }

    public function testChangeNodeValue()
    {
        $this->DOM->pickNode('id', 3)->changeData('email', 'foo-bar@mail.com');
        $check = $this->DOM->checkNode('email', 'foo-bar@mail.com');
        $this->assertTrue($check);
    }

    public function testChangeNodeValues()
    {
        $this->DOM->pickNode('id', 3)->changeData([
            'type' => 'user', 
            'email' => 'foo-bar@mail.com'
        ]);
        $check = $this->DOM->checkNode('type', 'user');
        $check2 = $this->DOM->checkNode('email', 'foo-bar@mail.com');
        $this->assertTrue($check);
        $this->assertTrue($check2);
    }

    public function testChangeAllNodesValues()
    {
        $this->DOM->pickNode('account')->changeData(['name' => 'NAME RESET']);
        $expected = [
            0 => 'NAME RESET',
            1 => 'NAME RESET',
            2 => 'NAME RESET',
            3 => 'NAME RESET',
            4 => 'NAME RESET',
            5 => 'NAME RESET',
            6 => 'NAME RESET'
        ];
        $data = $this->DOM->pickNode('account')->fetchData('name')->toArray();
        $this->assertEquals($expected, $data);
    }

    public function testSetNodeCDATAValue()
    {
        $this->DOM->pickNode('name', 'Third User')->setValue('New node value');
        $check = $this->DOM->checkNode('New node value');
        $this->assertTrue($check);
    }

    public function testSetNodeTextValue()
    {
        $this->DOM->pickNode('name', 'Third User')->setTextValue('New node value');
        $check = $this->DOM->checkNode('New node value');
        $this->assertTrue($check);
    }

    public function testRemoveAttribute()
    {
        $this->DOM->pickNode('id', 3)->changeData('email', false);
        $check = $this->DOM->checkNode('email', 'second-admin@mail.com');
        $this->assertFalse($check);
    }

    public function testRemoveNode()
    {
        $this->DOM->pickNode('email', 'second-user@mail.com')->remove();
        $check = $this->DOM->checkNode('email', 'second-user@mail.com');
        $this->assertFalse($check);
    }
}
