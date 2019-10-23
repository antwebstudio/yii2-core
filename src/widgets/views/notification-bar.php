<style>
body{
    margin: 0;
    padding: 0;
    width: 100%;
}

.marquee {
  height: 25px;
  /*width: 420px;*/

  overflow: hidden;
  position: relative;
}

.marquee div {
  display: block;
  width: 200%;
  height: 30px;

  position: absolute;
  overflow: hidden;

  animation: marquee 20s linear infinite;
}

.marquee span {
  float: left;
  width: 50%;
}

@keyframes marquee {
  0% { left: 0; }
  100% { left: -100%; }
}
#hellobar-bar {
    font-family: "Open Sans", sans-serif;
    width: 100%;
    margin: 0;
    height: 30px;
    display: table;
    font-size: 17px;
    font-weight: 400;
    padding: .33em .5em;
    -webkit-font-smoothing: antialiased;
    /*color: #5c5e60;*/
	color: #ffffff;
    /*position: fixed;*/
    background-color: #5c5e60;
    box-shadow: 0 1px 3px 2px rgba(0,0,0,0.15);
}
#hellobar-bar.regular {
    height: 30px;
    font-size: 14px;
    padding: .2em .5em;
}
.hb-content-wrapper {
    text-align: center;
    text-align: center;
    position: relative;
    display: table-cell;
    vertical-align: middle;
}
.hb-content-wrapper p {
    margin-top: 0;
    margin-bottom: 0;
}
.hb-text-wrapper {
    margin-right: .67em;
    display: inline-block;
    line-height: 1.3;
}
.hb-text-wrapper .hb-headline-text {
    font-size: 1em;
    display: inline-block;
    vertical-align: middle;
}
#hellobar-bar .hb-cta {
    display: inline-block;
    vertical-align: middle;
    margin: 5px 0;
    color: #ffffff;
    background-color: #22af73;
    border-color: #22af73
}
.hb-cta-button {
    opacity: 1;
    color: #fff;
    display: block;
    cursor: pointer;
    line-height: 1.5;
    max-width: 22.5em;
    text-align: center;
    position: relative;
    border-radius: 3px;
    white-space: nowrap;
    margin: 1.75em auto 0;
    text-decoration: none;
    padding: 0;
    overflow: hidden;
}
.hb-cta-button .hb-text-holder {
    border-radius: inherit;
    padding: 5px 15px;
}
.hb-close-wrapper {
    display: table-cell;
    width: 1.6em;
}
.hb-close-wrapper .icon-close {
    font-size: 14px;
    top: 15px;
    right: 25px;
    width: 15px;
    height: 15px;
    opacity: .3;
    color: #000;
    cursor: pointer;
    position: absolute;
    text-align: center;
    line-height: 15px;
    z-index: 1000;
    text-decoration: none;
}
</style>
<?php if ($content && $this->context->shouldShow()): ?>
<div id="hellobar-bar" class="regular closable">
	<?php if ($this->context->marquee): ?>
		<div class="marquee">
			<div>
				<span><?= $content ?></span>
				<span><?= $content ?></span>
			</div>
		</div>
	<?php else: ?>

		<div class="hb-content-wrapper">
			<div class="hb-text-wrapper">
				<div class="hb-headline-text">
					<p><span><?= $content ?></span></p>
				</div>
			</div>
			<?php /*
			<a href="http://www.codexworld.com" target="_blank" class="hb-cta hb-cta-button">
				<div class="hb-text-holder">
					<p>Register Now</p>
				</div>
			</a>
			*/?>
		</div>
	<?php endif ?>
    <div class="hb-close-wrapper">
        <a href="javascript:void(0);" class="icon-close">&#10006;</a>
    </div>
</div>
<?php endif ?>
<?php if (YII_DEBUG && YII_LOCALHOST): ?>
	<p>Now: <?= (new \ant\helpers\DateTime) ?></p>
	<p>Start Time: <?= $this->context->startAt ?></p>
	<p>End Time: <?= $this->context->endAt ?></p>
<?php endif ?>