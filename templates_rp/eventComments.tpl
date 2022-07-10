{if $commentList|count || $commentCanAdd}
	<section id="comments" class="section sectionContainerList">
		<h2 class="sectionTitle">{lang}wcf.global.comments{/lang}{if $event->comments} <span class="badge">{#$event->comments}</span>{/if}</h2>
		
		{include file='__commentJavaScript' commentContainerID='eventCommentList'}
		
		<ul id="eventCommentList" class="commentList containerList" data-can-add="{if $commentCanAdd}true{else}false{/if}" data-object-id="{@$eventID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
			{if $commentCanAdd}{include file='commentListAddComment' wysiwygSelector='eventCommentListAddComment'}{/if}
			{include file='commentList'}
		</ul>
	</section>
{/if}
