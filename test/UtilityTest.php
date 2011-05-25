<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreUtilityTest extends PHPUnit_Framework_TestCase {
  
  public function testIdentity() {
    // from js
    $moe = array('name'=>'moe');
    $moe_obj = (object) $moe;
    $this->assertEquals($moe, _::identity($moe));
    $this->assertEquals($moe_obj, _::identity($moe_obj));
  }
  
  public function testUniqueId() {
    // from js
    $ids = array();
    while($i++ < 100) array_push($ids, _::uniqueId());
    $this->assertEquals(count($ids), count(_::uniq($ids)));
    
    // extra
    $this->assertEquals('stooges_', join('', (_::first(_::uniqueId('stooges'), 8))), 'prefix assignment works');
  }
  
  public function testTimes() {
    $vals = array();
    _::times(3, function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'is 0 indexed');
    
    // @todo
    /*
    vals = [];
    _(3).times(function (i) { vals.push(i); });
    ok(_.isEqual(vals, [0,1,2]), "works as a wrapper");
    */
  }
  
  public function testMixin() {
    _::mixin(array(
      'myReverse' => function($string) {
        $chars = str_split($string);
        krsort($chars);
        return join('', $chars);
      }
    ));
    $this->assertEquals('aecanap', _::myReverse('panacea'), 'mixed in a function to _');
    
    // @todo
    /*
    equals(_('champ').myReverse(), 'pmahc', 'mixed in a function to the OOP wrapper');
    */
  }
  
  public function testTemplate() {
    // from js
    $basicTemplate = _::template("<%= thing %> is gettin' on my noives!");
    $this->assertEquals("This is gettin' on my noives!", $basicTemplate(array('thing'=>'This')), 'can do basic attribute interpolation');
    $this->assertEquals("This is gettin' on my noives!", $basicTemplate((object) array('thing'=>'This')), 'can do basic attribute interpolation');
    /*
        equals(result, "This is gettin' on my noives!", 'can do basic attribute interpolation');

        var backslashTemplate = _.template("<%= thing %> is \\ridanculous");
        equals(backslashTemplate({thing: 'This'}), "This is \\ridanculous");

        var fancyTemplate = _.template("<ul><% \
          for (key in people) { \
        %><li><%= people[key] %></li><% } %></ul>");
        result = fancyTemplate({people : {moe : "Moe", larry : "Larry", curly : "Curly"}});
        equals(result, "<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", 'can run arbitrary javascript in templates');

        var namespaceCollisionTemplate = _.template("<%= pageCount %> <%= thumbnails[pageCount] %> <% _.each(thumbnails, function(p) { %><div class=\"thumbnail\" rel=\"<%= p %>\"></div><% }); %>");
        result = namespaceCollisionTemplate({
          pageCount: 3,
          thumbnails: {
            1: "p1-thumbnail.gif",
            2: "p2-thumbnail.gif",
            3: "p3-thumbnail.gif"
          }
        });
        equals(result, "3 p3-thumbnail.gif <div class=\"thumbnail\" rel=\"p1-thumbnail.gif\"></div><div class=\"thumbnail\" rel=\"p2-thumbnail.gif\"></div><div class=\"thumbnail\" rel=\"p3-thumbnail.gif\"></div>");

        var noInterpolateTemplate = _.template("<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>");
        result = noInterpolateTemplate();
        equals(result, "<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>");

        var quoteTemplate = _.template("It's its, not it's");
        equals(quoteTemplate({}), "It's its, not it's");

        var quoteInStatementAndBody = _.template("<%\
          if(foo == 'bar'){ \
        %>Statement quotes and 'quotes'.<% } %>");
        equals(quoteInStatementAndBody({foo: "bar"}), "Statement quotes and 'quotes'.");

        var withNewlinesAndTabs = _.template('This\n\t\tis: <%= x %>.\n\tok.\nend.');
        equals(withNewlinesAndTabs({x: 'that'}), 'This\n\t\tis: that.\n\tok.\nend.');

        if (!$.browser.msie) {
          var fromHTML = _.template($('#template').html());
          equals(fromHTML({data : 12345}).replace(/\s/g, ''), '<li>24690</li>');
        }

        _.templateSettings = {
          evaluate    : /\{\{([\s\S]+?)\}\}/g,
          interpolate : /\{\{=([\s\S]+?)\}\}/g
        };

        var custom = _.template("<ul>{{ for (key in people) { }}<li>{{= people[key] }}</li>{{ } }}</ul>");
        result = custom({people : {moe : "Moe", larry : "Larry", curly : "Curly"}});
        equals(result, "<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", 'can run arbitrary javascript in templates');

        var customQuote = _.template("It's its, not it's");
        equals(customQuote({}), "It's its, not it's");

        var quoteInStatementAndBody = _.template("{{ if(foo == 'bar'){ }}Statement quotes and 'quotes'.{{ } }}");
        equals(quoteInStatementAndBody({foo: "bar"}), "Statement quotes and 'quotes'.");

        _.templateSettings = {
          evaluate    : /<\?([\s\S]+?)\?>/g,
          interpolate : /<\?=([\s\S]+?)\?>/g
        };

        var customWithSpecialChars = _.template("<ul><? for (key in people) { ?><li><?= people[key] ?></li><? } ?></ul>");
        result = customWithSpecialChars({people : {moe : "Moe", larry : "Larry", curly : "Curly"}});
        equals(result, "<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", 'can run arbitrary javascript in templates');

        var customWithSpecialCharsQuote = _.template("It's its, not it's");
        equals(customWithSpecialCharsQuote({}), "It's its, not it's");

        var quoteInStatementAndBody = _.template("<? if(foo == 'bar'){ ?>Statement quotes and 'quotes'.<? } ?>");
        equals(quoteInStatementAndBody({foo: "bar"}), "Statement quotes and 'quotes'.");

        _.templateSettings = {
          interpolate : /\{\{(.+?)\}\}/g
        };

        var mustache = _.template("Hello {{planet}}!");
        equals(mustache({planet : "World"}), "Hello World!", "can mimic mustache.js");
    */
  }
}