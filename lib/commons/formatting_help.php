<?php require_once 'commons/settings_light.php'; ?>
<p>You may use these tags (<a href="http://<?= $SETTINGS['wiki_domain'] ?>/HTML">and many others!</a>) to format your text...</p>
<table>
<tr class="titlerow"><th class="centered">Tag</th><th class="centered">Effect</th><td style="background-color: white;"></td><th class="centered">Example</th><th class="centered">Result</th></tr>
<tr class="row">
<td>&lt;em&gt;TEXT&lt;/em&gt;</td>
<td>Make TEXT <em>emphasized</em></td>
<td style="background-color: white;"></td>
<td>That is &lt;em&gt;so&lt;/em&gt; true!</td>
<td>That is <em>so</em> true!</td>
</tr>
<tr class="altrow">
<td>&lt;strong&gt;TEXT&lt;/strong&gt;</td>
<td>Make TEXT <strong>strong</strong></td>
<td style="background-color: white;"></td>
<td>&lt;strong&gt;Phenomenal, cosmic power!&lt;/strong&gt;</td>
<td><strong>Phenomenal, cosmic power!</strong></td>
</tr>
<tr class="row">
<td>&lt;img src="URL"&gt;</td>
<td>Displays the image at the URL</td>
<td style="background-color: white;"></td>
<td>&lt;img src="/gfx/items/fruit/banana.png"&gt;</td>
<td><img src="/gfx/items/fruit/banana.png" /></td>
</tr>
<tr class="altrow">
<td>&lt;a href="URL"&gt;TEXT&lt;/a&gt;</td>
<td>Makes TEXT a link to URL</td>
<td style="background-color: white;"></td>
<td>Visit &lt;a href="http://<?= $SETTINGS['site_domain'] ?>"&gt;<?= $SETTINGS['site_name'] ?>&lt;/a&gt;!</td>
<td>Visit <a href="http://<?= $SETTINGS['site_domain'] ?>"><?= $SETTINGS['site_name'] ?></a>!</td>
</tr>
</table>
<p>For more help on using HTML, refer to the <a href="http://<?= $SETTINGS['wiki_domain'] ?>/HTML">HTML</a> page (on <a href="http://<?= $SETTINGS['wiki_domain'] ?>/">PsyHelp</a>).</p>
