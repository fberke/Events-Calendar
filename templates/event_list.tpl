<div id="event_list">
{if isset($events)}
<ul>
{loop $events}
	<li>
		<h2>{$date}</h2>
		<ul>
		{loop $entries}
			<li>
				<h3><span style="padding:10px;background-color:{$eventColor};">{$eventTitle}</span></h3>
				
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
<a href="{$prevMonthLink}" title="{$prevMonthName}">{$prevMonthLinkText}</a>
<a href="{$nextMonthLink}" title="{$nextMonthName}">{$nextMonthLinkText}</a>
</div>