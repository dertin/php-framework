{config_load file='default.conf'}{strip}
<p>Hello World</p>
{foreach $people as $person}
  {$person->PersonId}
{/foreach}
{* <span>{$PersonName}</span> *}
{/strip}
