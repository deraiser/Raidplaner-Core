<article class="message messageReduced">
	<section class="messageContent">
		<header class="messageHeader">
			<div class="box32 messageHeaderWrapper">
				{user object=$event->getUserProfile() type='avatar32' ariaHidden='true' tabindex='-1'}
				
				<div class="messageHeaderBox">
					<h2 class="messageTitle">
						<a href="{@$event->getLink()}">{$event->getTitle()}</a>
					</h2>
					
					<ul class="messageHeaderMetaData">
						<li>{user object=$event->getUserProfile() class='username'}</li>
						<li><span class="messagePublicationTime">{@$event->getTime()|time}</span></li>
						
						{event name='messageHeaderMetaData'}
					</ul>
				</div>
			</div>
			
			{event name='messageHeader'}
		</header>
		
		<div class="messageBody">
			{event name='beforeMessageText'}
			
			<div class="messageText">
                {include file=$event->getController()->getModerationTemplate() application='rp'}
			</div>
			
			{event name='afterMessageText'}
		</div>
	</section>
</article>
