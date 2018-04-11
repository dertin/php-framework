{config_load file='default.conf'}{strip}
<p>Hello World</p>
{foreach $people as $person}
  <span> {$person->PersonId}</span>
  <span> {$person->PersonName}</span>
  </br>
{/foreach}
{foreach $books as $book}
  <span> {$book->BookId}</span>
  <span> {$book->BookTitle}</span>
  </br>
{/foreach}
<br>
<span>{$ResultDelete}</span>
{* <span>{$PersonName}</span> *}
{/strip}
