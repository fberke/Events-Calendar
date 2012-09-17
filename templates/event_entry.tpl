<div id="event_entry">
	<h1>{$eventTitle}<span title="{$eventCategory}" style="background-color:{$eventColor};">&nbsp;</span></h1>
	<aside>
		<div>
		<h4>{$eventDateStartTitle}</h4>
		{$eventDateStart} ({$eventTimeStart})
		</div>
		<div>
		<h4>{$eventDateEndTitle}</h4>
		{$eventDateEnd} ({$eventTimeEnd})
		</div>
		<div>
		<h4>{$eventCategoryTitle}</h4>
		{$eventCategory}
		</div>
	</aside>
	
	{if $eventImageURL != ''}
	<div class="image">
		<img src="{$eventImageURL}" title="" alt="" />
	</div>
	{/if}
	
	<div>
		{$eventDescription}
	</div>
	
	{if $eventDroplet != ''}
	<div>
		{$eventDroplet}
	</div>
	{/if}
</div>
<div class="prevnext">
{if $prevEventLink != false}
<span><a href="{$prevEventLink}">{$prevEventLinkTitle}</a></span>
{/if}
{if $nextEventLink != false}
<span><a href="{$nextEventLink}">{$nextEventLinkTitle}</a></span>
{/if}
</div>
<div class="go_back"><a title="{$eventListLinkTitle}" href="{$eventListLink}">{$eventListLinkTitle}</a></div>

