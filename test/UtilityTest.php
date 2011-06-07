<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreUtilityTest extends PHPUnit_Framework_TestCase {

  public function testIdentity() {
    // from js
    $moe = array('name'=>'moe');
    $moe_obj = (object) $moe;
    $this->assertEquals($moe, _::identity($moe));
    $this->assertEquals($moe_obj, _::identity($moe_obj));

    // extra
    $this->assertEquals($moe, _($moe)->identity());
    $this->assertEquals($moe_obj, _($moe_obj)->identity());
  }

  public function testUniqueId() {
    // from js
    $ids = array();
    while($i++ < 100) array_push($ids, _::uniqueId());
    $this->assertEquals(count($ids), count(_::uniq($ids)));

    // extra
    $this->assertEquals('stooges', join('', (_::first(_::uniqueId('stooges'), 7))), 'prefix assignment works');
    $this->assertEquals('stooges', join('', _(_('stooges')->uniqueId())->first(7)), 'prefix assignment works in OO-style call');

    while($i++ < 100) array_push($ids, _()->uniqueId());
    $this->assertEquals(count($ids), count(_()->uniq($ids)));
  }

  public function testTimes() {
    // from js
    $vals = array();
    _::times(3, function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'is 0 indexed');

    $vals = array();
    _(3)->times(function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'works as a wrapper in OO-style call');
  }

  public function testMixin() {
    // from js
    _::mixin(array(
      'myReverse' => function($string) {
        $chars = str_split($string);
        krsort($chars);
        return join('', $chars);
      }
    ));
    $this->assertEquals('aecanap', _::myReverse('panacea'), 'mixed in a function to _');
    $this->assertEquals('pmahc', _('champ')->myReverse(), 'mixed in a function to _ with OO-style call');
  }

  public function testTemplate() {
    // from js
    $basicTemplate = _::template('<%= $thing %> is gettin on my noives!');
    $this->assertEquals("This is gettin on my noives!", $basicTemplate(array('thing'=>'This')), 'can do basic attribute interpolation');
    $this->assertEquals("This is gettin on my noives!", $basicTemplate((object) array('thing'=>'This')), 'can do basic attribute interpolation');

    $backslashTemplate = _::template('<%= $thing %> is \\ridanculous');
    $this->assertEquals('This is \\ridanculous', $backslashTemplate(array('thing'=>'This')));

    $fancyTemplate = _::template('<ul><% foreach($people as $key=>$name) { %><li><%= $name %></li><% } %></ul>');
    $result = $fancyTemplate(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals('<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>', $result, 'can run arbitrary php in templates');

    $namespaceCollisionTemplate = _::template('<%= $pageCount %> <%= $thumbnails[$pageCount] %> <% _::each($thumbnails, function($p) { %><div class=\"thumbnail\" rel=\"<%= $p %>\"></div><% }); %>');
    $result = $namespaceCollisionTemplate((object) array(
      'pageCount' => 3,
      'thumbnails'=> array(
        1 => 'p1-thumbnail.gif',
        2 => 'p2-thumbnail.gif',
        3 => 'p3-thumbnail.gif'
      )
    ));
    $expected = '3 p3-thumbnail.gif <div class=\"thumbnail\" rel=\"p1-thumbnail.gif\"></div><div class=\"thumbnail\" rel=\"p2-thumbnail.gif\"></div><div class=\"thumbnail\" rel=\"p3-thumbnail.gif\"></div>';
    $this->assertEquals($expected, $result);

    $noInterpolateTemplate = _::template("<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>");
    $result = $noInterpolateTemplate();
    $expected = "<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>";
    $this->assertEquals($expected, $result);

    $quoteTemplate = _::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $quoteTemplate(new StdClass));

    $quoteInStatementAndBody = _::template('<%
      if($foo == "bar"){
    %>Statement quotes and \'quotes\'.<% } %>');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody((object) array('foo'=>'bar')));

    $withNewlinesAndTabs = _::template('This\n\t\tis: <%= $x %>.\n\tok.\nend.');
    $this->assertEquals('This\n\t\tis: that.\n\tok.\nend.', $withNewlinesAndTabs((object) array('x'=>'that')));

    _::templateSettings(array(
      'evaluate'    => '/\{\{([\s\S]+?)\}\}/',
      'interpolate' => '/\{\{=([\s\S]+?)\}\}/'
    ));

    $custom = _::template('<ul>{{ foreach($people as $key=>$name) { }}<li>{{= $people[$key] }}</li>{{ } }}</ul>');
    $result = $custom(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals("<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", $result, 'can run arbitrary php in templates using custom tags');

    $customQuote = _::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $customQuote(new StdClass));

    $quoteInStatementAndBody = _::template('{{ if($foo == "bar"){ }}Statement quotes and \'quotes\'.{{ } }}');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody(array('foo'=>'bar')));

    _::templateSettings(array(
      'evaluate'    => '/<\?([\s\S]+?)\?>/',
      'interpolate' => '/<\?=([\s\S]+?)\?>/'
    ));

    $customWithSpecialChars = _::template('<ul><? foreach($people as $key=>$name) { ?><li><?= $people[$key] ?></li><? } ?></ul>');
    $result = $customWithSpecialChars(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals("<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", $result, 'can run arbitrary php in templates');

    $customWithSpecialCharsQuote = _::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $customWithSpecialCharsQuote(new StdClass));

    $quoteInStatementAndBody = _::template('<? if($foo == "bar"){ ?>Statement quotes and \'quotes\'.<? } ?>');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody(array('foo'=>'bar')));

    _::templateSettings(array(
      'interpolate' => '/\{\{(.+?)\}\}/'
    ));

    $mustache = _::template('Hello {{$planet}}!');
    $this->assertEquals("Hello World!", $mustache(array('planet'=>'World')), 'can mimic mustache.js');

    // extra
    _::templateSettings(); // reset to default
    $basicTemplate = _::template('<%= $thing %> is gettin\' on my <%= $nerves %>!');
    $this->assertEquals("This is gettin' on my noives!", $basicTemplate(array('thing'=>'This', 'nerves'=>'noives')), 'can do basic attribute interpolation for multiple variables');

    $result = _('hello: <%= $name %>')->template(array('name'=>'moe'));
    $this->assertEquals('hello: moe', $result, 'works with OO-style call');

    $result = _('<%= $thing %> is gettin\' on my <%= $nerves %>!')->template(array('thing'=>'This', 'nerves'=>'noives'));
    $this->assertEquals("This is gettin' on my noives!", $result, 'can do basic attribute interpolation for multiple variables with OO-style call');
  
    $result = _('<%
      if($foo == "bar"){
    %>Statement quotes and \'quotes\'.<% } %>')->template((object) array('foo'=>'bar'));
    $this->assertEquals("Statement quotes and 'quotes'.", $result);
  }
}