<div id="event_list">
{if ($events != false)}
<ul>
{loop $events}
	<li>
		<h2>{$date}</h2>
		<ul>
		{loop $entries}
			<li>
				<h3>{$eventTitle}<span title="{$eventCategory}" style="background-color:{$eventColor};">&nbsp;</span></h3>
				
				<div class="content_left">
				{$eventSummary}
				<p><a href="{$eventDetailsLink}" title="{$eventDetailsLinkTitle}{$eventTitle}">
				<strong>{$eventDetailsLinkTitle}{$eventTitle}</strong>
				</a></p>
				</div>
				
				<div class="content_right">
					<table>
						<tr>
							<td>{$eventDateStartTitle}</td>
							<td>{$eventDateStart}</td>
						</tr>
						<tr>
							<td>{$eventDateEndTitle}</td>
							<td>{$eventDateEnd}</td>
						</tr>
						<tr>
							<td>{$eventCategoryTitle}</td>
							<td>{$eventCategory}</td>
						</tr>
					</table>
				</div>
			</li>
		{/loop}
		</ul>
	</li>
{/loop}
</ul>
{else}
<p>{$noDates}</p>
{/if}
<div class="prevnext">
<span class="prev"><a href="{$prevMonthLink}" title="{$prevMonthName}">{$prevMonthLinkText}</a></span>
<span class="next"><a href="{$nextMonthLink}" title="{$nextMonthName}">{$nextMonthLinkText}</a></span>
</div>
</div>