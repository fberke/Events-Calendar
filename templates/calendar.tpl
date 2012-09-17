<div class="eventscalendar">
<table>
	<caption>
	<div class="previous">
		<span><a href="{$caption.previousYearLink}" title="{$caption.previousYearLinkTitle}">&laquo;</a></span>
		<span><a href="{$caption.previousMonthLink}" title="{$caption.previousMonthLinkTitle}">&lsaquo;</a></span>
	</div>
	{$caption.monthname}&nbsp;{$caption.year}
	<div class="next">
		<span><a href="{$caption.nextMonthLink}" title="{$caption.nextMonthLinkTitle}">&rsaquo;</a></span>
		<span><a href="{$caption.nextYearLink}" title="{$caption.nextYearLinkTitle}">&raquo;</a></span>
	</div>
	</caption>
	
	<thead>
	<tr>
		{foreach $thead weekdays}
		<th>{truncate_weekday($weekdays)}<span class="position_outside">{truncate_weekday($weekdays, 1)}</span></th>
		{/foreach}
	</tr>
	</thead>
	
	<tbody>
	{loop $rows}
	<tr>
		{loop $cells}
		{if $dayType == 'normal'}
		<td {if ($isToday)}class="today"{/if}>
			<span>{$dayNr}<span class="position_outside">. {$monthname}</span></span>
		</td>
		{elseif $dayType == 'event'}
		<td class="{if $isToday}today {/if}isevent">
			<a href="{$eventListLink}">
				<span>{$dayNr}<span class="position_outside">. {$monthname}</span></span>
			</a>
			<div class="calendar-event">
				<h5>{$eventListHeading}</h5>
				<ul>
				{loop $events}
					<li>
					<a href="{$eventDetailsLink}" title="{$eventDetailsLinkTitle}{$eventTitle}">
					<div style="background-color:{$eventColor};">
						<h6>{$eventTitle}</h6>
						<p>{$eventOneliner}</p>
						<p>{$eventTime}</p>
					</div>
					</a>
					</li>
				{/loop}
				</ul>
			</div>
		</td>
		{elseif $dayType == 'eventBE'}
		<td class="{if $isToday}today {/if}isevent">
			<a href="{$eventListLink}">
				<span>{$dayNr}<span class="position_outside">. {$monthname}</span></span>
			</a>
		</td>
		{elseif $dayType == 'noday'}
		<td class="noday">
			<span>&nbsp;</span>
		</td>
		{/if}
		{/loop}
	</tr>
	{/loop}
	</tbody>
</table>
</div>
