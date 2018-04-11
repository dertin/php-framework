{config_load file='default.conf'}{strip}
<p>Hello World</p>
{foreach $people as $person}
  <span> {$person->PersonId}</span>
  <span> {$person->PersonName}</span>
  </br>
{/foreach}
{* <span>{$PersonName}</span> *}
{/strip}
