<?php

class UnderscoreUtilityTest extends PHPUnit_Framework_TestCase {

  public function testIdentity() {
    // from js
    $moe = array('name'=>'moe');
    $moe_obj = (object) $moe;
    $this->assertEquals($moe, __::identity($moe));
    $this->assertEquals($moe_obj, __::identity($moe_obj));

    // extra
    $this->assertEquals($moe, __($moe)->identity());
    $this->assertEquals($moe_obj, __($moe_obj)->identity());
    
    // docs
    $moe = array('name'=>'moe');
    $this->assertTrue($moe === __::identity($moe));
  }

  public function testUniqueId() {
    // docs
    $this->assertEquals(0, __::uniqueId());
    $this->assertEquals('stooge_1', __::uniqueId('stooge_'));
    $this->assertEquals(2, __::uniqueId());
    
    // from js
    $ids = array();
    $i = 0;
    while($i++ < 100) array_push($ids, __::uniqueId());
    $this->assertEquals(count($ids), count(__::uniq($ids)));

    // extra
    $this->assertEquals('stooges', join('', (__::first(__::uniqueId('stooges'), 7))), 'prefix assignment works');
    $this->assertEquals('stooges', join('', __(__('stooges')->uniqueId())->first(7)), 'prefix assignment works in OO-style call');

    while($i++ < 100) array_push($ids, __()->uniqueId());
    $this->assertEquals(count($ids), count(__()->uniq($ids)));
  }

  public function testTimes() {
    // from js
    $vals = array();
    __::times(3, function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'is 0 indexed');

    $vals = array();
    __(3)->times(function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'works as a wrapper in OO-style call');
  
    // docs
    $result = '';
    __::times(3, function() use (&$result) { $result .= 'a'; });
    $this->assertEquals('aaa', $result);
  }

  public function testMixin() {
    // from js
    __::mixin(array(
      'myReverse' => function($string) {
        $chars = str_split($string);
        krsort($chars);
        return join('', $chars);
      }
    ));
    $this->assertEquals('aecanap', __::myReverse('panacea'), 'mixed in a function to _');
    $this->assertEquals('pmahc', __('champ')->myReverse(), 'mixed in a function to _ with OO-style call');
    
    // docs
    __::mixin(array(
      'capitalize'=> function($string) { return ucwords($string); },
      'yell'      => function($string) { return strtoupper($string); }
    ));
    $this->assertEquals('Moe', __::capitalize('moe'));
    $this->assertEquals('MOE', __::yell('moe'));
  }

  public function testTemplate() {
    // from js
    $basicTemplate = __::template('<%= $thing %> is gettin on my noives!');
    $this->assertEquals("This is gettin on my noives!", $basicTemplate(array('thing'=>'This')), 'can do basic attribute interpolation');
    $this->assertEquals("This is gettin on my noives!", $basicTemplate((object) array('thing'=>'This')), 'can do basic attribute interpolation');

    $backslashTemplate = __::template('<%= $thing %> is \\ridanculous');
    $this->assertEquals('This is \\ridanculous', $backslashTemplate(array('thing'=>'This')));
    
    $escapeTemplate = __::template('<%= $a ? "checked=\\"checked\\"" : "" %>');
    $this->assertEquals('checked="checked"', $escapeTemplate(array('a'=>true)), 'can handle slash escapes in interpolations');

    $fancyTemplate = __::template('<ul><% foreach($people as $key=>$name) { %><li><%= $name %></li><% } %></ul>');
    $result = $fancyTemplate(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals('<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>', $result, 'can run arbitrary php in templates');

    $namespaceCollisionTemplate = __::template('<%= $pageCount %> <%= $thumbnails[$pageCount] %> <% __::each($thumbnails, function($p) { %><div class=\"thumbnail\" rel=\"<%= $p %>\"></div><% }); %>');
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

    $noInterpolateTemplate = __::template("<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>");
    $result = $noInterpolateTemplate();
    $expected = "<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>";
    $this->assertEquals($expected, $result);

    $quoteTemplate = __::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $quoteTemplate(new StdClass));

    $quoteInStatementAndBody = __::template('<%
      if($foo == "bar"){
    %>Statement quotes and \'quotes\'.<% } %>');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody((object) array('foo'=>'bar')));

    $withNewlinesAndTabs = __::template('This\n\t\tis: <%= $x %>.\n\tok.\nend.');
    $this->assertEquals('This\n\t\tis: that.\n\tok.\nend.', $withNewlinesAndTabs((object) array('x'=>'that')));
    
    $template = __::template('<i><%- $value %></i>');
    $result = $template((object) array('value'=>'<script>'));
    $this->assertEquals('<i>&lt;script&gt;</i>', $result);

    __::templateSettings(array(
      'evaluate'    => '/\{\{([\s\S]+?)\}\}/',
      'interpolate' => '/\{\{=([\s\S]+?)\}\}/'
    ));

    $custom = __::template('<ul>{{ foreach($people as $key=>$name) { }}<li>{{= $people[$key] }}</li>{{ } }}</ul>');
    $result = $custom(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals("<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", $result, 'can run arbitrary php in templates using custom tags');

    $customQuote = __::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $customQuote(new StdClass));

    $quoteInStatementAndBody = __::template('{{ if($foo == "bar"){ }}Statement quotes and \'quotes\'.{{ } }}');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody(array('foo'=>'bar')));

    __::templateSettings(array(
      'evaluate'    => '/<\?([\s\S]+?)\?>/',
      'interpolate' => '/<\?=([\s\S]+?)\?>/'
    ));

    $customWithSpecialChars = __::template('<ul><? foreach($people as $key=>$name) { ?><li><?= $people[$key] ?></li><? } ?></ul>');
    $result = $customWithSpecialChars(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals("<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", $result, 'can run arbitrary php in templates');

    $customWithSpecialCharsQuote = __::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $customWithSpecialCharsQuote(new StdClass));

    $quoteInStatementAndBody = __::template('<? if($foo == "bar"){ ?>Statement quotes and \'quotes\'.<? } ?>');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody(array('foo'=>'bar')));

    __::templateSettings(array(
      'interpolate' => '/\{\{(.+?)\}\}/'
    ));

    $mustache = __::template('Hello {{$planet}}!');
    $this->assertEquals("Hello World!", $mustache(array('planet'=>'World')), 'can mimic mustache.js');

    // extra
    __::templateSettings(); // reset to default
    $basicTemplate = __::template('<%= $thing %> is gettin\' on my <%= $nerves %>!');
    $this->assertEquals("This is gettin' on my noives!", $basicTemplate(array('thing'=>'This', 'nerves'=>'noives')), 'can do basic attribute interpolation for multiple variables');

    $result = __('hello: <%= $name %>')->template(array('name'=>'moe'));
    $this->assertEquals('hello: moe', $result, 'works with OO-style call');

    $result = __('<%= $thing %> is gettin\' on my <%= $nerves %>!')->template(array('thing'=>'This', 'nerves'=>'noives'));
    $this->assertEquals("This is gettin' on my noives!", $result, 'can do basic attribute interpolation for multiple variables with OO-style call');
  
    $result = __('<%
      if($foo == "bar"){
    %>Statement quotes and \'quotes\'.<% } %>')->template((object) array('foo'=>'bar'));
    $this->assertEquals("Statement quotes and 'quotes'.", $result);
    
    // docs
    $compiled = __::template('hello: <%= $name %>');
    $result = $compiled(array('name'=>'moe'));
    $this->assertEquals('hello: moe', $result);
    
    $list = '<% __::each($people, function($name) { %><li><%= $name %></li><% }); %>';
    $result = __::template($list, array('people'=>array('moe', 'curly', 'larry')));
    $this->assertEquals('<li>moe</li><li>curly</li><li>larry</li>', $result);
    
    __::templateSettings(array(
      'interpolate' => '/\{\{(.+?)\}\}/'
    ));
    $mustache = __::template('Hello {{$planet}}!');
    $result = $mustache(array('planet'=>'World'));
    $this->assertEquals('Hello World!', $result);
    
    $template = __::template('<i><%- $value %></i>');
    $result = $template(array('value'=>'<script>'));
    $this->assertEquals('<i>&lt;script&gt;</i>', $result);
    
    $sans = __::template('A <% $this %> B');
    $this->assertEquals('A  B', $sans());
  }
  
  public function testEscape() {
    // from js
    $this->assertEquals('Curly &amp; Moe', __::escape('Curly & Moe'));
    $this->assertEquals('Curly &amp;amp; Moe', __::escape('Curly &amp; Moe'));
    
    // extra
    $this->assertEquals('Curly &amp; Moe', __('Curly & Moe')->escape());
    $this->assertEquals('Curly &amp;amp; Moe', __('Curly &amp; Moe')->escape());
  }
}