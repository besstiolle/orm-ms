<style>
	label.help{
		
	}
	
	div.left{
	    float: left;
		text-align: center;
		width: 50px;
	}
	
	div.right{
		border-left: 2px solid #CCCCCC;
		float: left;
		padding-left: 5px;
	}
	.ormbutton {
		clear: both;
		text-align: left
	}
	a.ormbutton{
		display: inline-block;
		position: relative;
		margin: 10px 0;
		line-height: 26px;
		color: #232323;
		text-decoration: none;
		padding: 1px 8px 2px 20px;
	}
	a.ormbutton .ui-icon {
		position: absolute;
		left: 0;
		top: 6px;
	}
	a.ormbutton:hover {
		color: #fff
	}
</style>


{$formstart}

<h3>Entity Generator</h3>
	<div class='left'><img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAHd0lEQVRYR8WXa2wU1xXH/7MzO7M7+/Jrba/XDwwGA04JkBiIQoA0ThrFKShBJLQfUto8lPRLFSURSqSqkKoSaj6Qh0JUtahq86USoWqitighJMU8gmwgTlwabAwGFmM7jtePXfYxs7vTc+56yWLM2pYiZaRr7+w8zu/+z/mfe1fC93xIM8XfvmNXrc/nWVVS5FvlcnuWa5paYpdlaA6t1m63+61MBsmk2WeaRjhFn2Px2OVo5FrnaHi0PTww2r57945woRjTAjyzfbuvqX7xkzU11S9VVvgrizweOJwOyDZAoT82m40+y5BsEqyMhXQ6DZOGTbLBMEykLQvxRAKjY+MYHBzqvBQKvdFx5JN39+3bl54KcxPAq6+9/tPbl922p3HBfB9fNFMp+mtBopfLFFhV7WAFJBp8nqZZZyh4igfdmyQAhrLoKZskQdVUAszgv2e7+0+e6nr09zu3t+dD3ACwZcsWdesTT400NTa4I9eiSJopEVjjoIoC1a6AZBfnMp3nANKTgU3ThEHPMDSDMBirpdFzLpcL7Z1fdv7ssUdW3BLAsizpxMnODMs9PDJCwSWSnIJSMBFYywZX6bNiV+manJ25aVBgUwSlegCDMEQqzeqREpSu8pJijEyMD65f3VxF72WBxHFTCjo+77Lq66pxZWAQA0PDIr92hYKqKnSHSsWnwqFqBKSSCpSCVIYCGkgYSSQTBmI0DMMggCyErjsR8PtRVVmB7t4LQ3evWll5SwX4wqEjn1l11UEx0yKfVwBci8cRjcZodgnIJCcrQGkVRWmkqK4yNEgtI2mASgIetwu6S4fH5YSN5jg6PiFS03O+L/RQy/raGQGqA5UYj0TEfR63G0VeN+VQp1yqHEcIJ1N0ThEflDoBykeKyJLJJCKxGMYocCKRFPf5yEm9fZfmBsAvlinPnGuF6oAtaOdC5ALkQS9my3ERcg1wtYviIxm4NnJQXIhzAghSvlgBflE+ALtA2DAHMGlDBuB8myQzA4jzPAB+BwOcv3h5ZgUOth2zqsrLMRGNZl3ADuDZX7dhngLTAHCuuR8wECvAKvLhpVReCIWuPNxyb01BG/7neHuGi4g7mbCbkD6bgmwfmB1ALgUZ7pRUpE5yzvBYeOCBdWuDBW14rOO0Na+6CqGrg/hmdEw0EqfmgE69waU7JjuhIlozg3EnZNslqPA4BQnqA3FyTZzOuQBtdI+fekAwUIGe3r6Be9eurirogo/bjgsbcoEXkw3j5OuxiYhISYwqmy9w4DhZjls0z1CmNYEV4plKZDwvKejxuFDi9QrF2IbsHLZh6/0bCtvw4OHjVm0wcN2GutNJftaFDZ2aRjNiZ3NAWg8mbZires63SX2BFYjG4ohEr4mumLNh9/mLoR8/MAPARwRQNwnwXdtwVgAfHj5m1VVlFZitDT8+F8eejjj6J6gNzuKgifXSeLl/523v3bQWHPj0qFVLBTMXG963dxi/fjCIRRW6WIYLHRywZyiGF/8RCl/Z0VR6AwCvhoeOnsgUeT1zsmHznq+x+a46dAykQDVZ8KB6RXNAwf7PLiH0m6WTVZT3SNuJDmtBXc2cbMgA62+vRe9odj1YE1SwbZmGUqeEruE03uxIYCzxLVlDsYzDX1yeHuDAJ21Ww7y6Odnw2aNlWPeDGvSG01hRqeCdVhf+fnoIXaFxbFxZJfrHkx+QIyZLpKFERltXaHqAfx9qEwrkVsN8G+oOx7Q2rPvtWdzTlAXY9UPagsUn8ML7/ZAdbtgTYfzrVyuxs81Ae39WIQY4cmaWAFMXo+laMadg7VICGElj78My2vsm8IczruxSTc3pj60yPuhO4Z8XlCxAqYyj/ysAsLC+VnS/qX3gVmsBA9y9qBrnCOD5lTHc01iGJ/ZTMzIsNFXI+NMjbvzkz5dxMekTAAsJ4FjPlZtTQJtSeeu2p5LLly6R47zFon7OXYy34IUWIwZYUx9EDxWcMxbC355dCK+uYSBiYUGpDRMxA4//ZRRhUxMAi/wyTvT1XwdgK+aGvGv3279bs7r5xcpyv+jxonbJWxL/FqA2zC2Y14L8XfGdbw/hjsoA+ZuW30wKtvBX2NRM+8AyNzp7v8EvWupRXurFtr9GEI5lqF/IODU4wADsSsg0ODkqD13X1R+1blqydt2GnzcuadzsLy528sLipALUaGOq0EZUpp2yjR7lLbtlZbDs9X4s9wXQPZjdBcOinXJsBFaKVkPVBcUYxrvPN+HTczbsPWKgkZzSOS4AFAbg4ALA7XZrtIlQKfd8TltOSC2tGxfPb1iwtMIfaPR6PWU+r7fB7dHttEu2K5LiSFmpxHPHA/PvKAng7K0aEUEmh7+C7CyG6q3CYmpEp8LfAjAEB+PBauQUsXk8HplgbJND4v956RJdlL6Ty14+2bV5RdCVSMuwkzK8W57a4zmVvJM2KZ0OOY39p69GqRUXT70vvx7yP3Os3L03PEO/eIq8T+/7pVw87xW6wPAzHgSTzoxdffXq7pa3Zvx1POPbACenj9Sy0+p5XbE8pYRQPMhRGR60y0pHIhGTvjP+D1fcCYLZGAqaAAAAAElFTkSuQmCC' /></div>
<div class='right'>
	<label for='{$id}check' class='help' />Will propose you a php version of an entity based on its mysql table. Learn more about these options on <a href='#' target='_blank'>the wiki</a>.</label>
	<br/>
	<a class="ormbutton ui-state-default ui-corner-all" href="{$generateUrl}">
		<span class="ui-icon ui-icon-circle-check"></span>
		Generate
	</a>
</div>
<div class='clear'></div>

<div class='line'>
	<h3>Auto-Checking</h3>
	<div class='left'><img src='data:image/png;base64,
iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gcDDxc7+6GOjgAAA0lJREFUWMO9ls9rXFUUxz/33PfmZeZNXrroJCNDHVIhoBRCN+IIRenOhYh04UJ0qwsXrgQRFypC3flH9A9oF6LgQlBUaECKpSRCDOkPtDFqDGnmZfLe3ONiMmle5kdeJtOczePdH+d87znfc7/XcMCe/uwHqYSFujHGGoabAsbARtzmzITlqPVdcwZ9sBHfXfvkpRTAOzg5FdgLmzvp94A/yIHpBgdSNbw6F3Ljt0cU5PG4DgduLtaiK9/A1z0AkrbznVJWxSZt7XHkieH5qkfqIHHw9jMp3y7+zscXq1y/L/gCYmDxnzabLdcD3N/La+CZcN9nP5QTvuXdxlN4hwph3S4XkmVSYzv/wJX5GWT3Ee+d76wRddw7X2PTRpm9badc++UhWzvpHpwhAF6oR3xwud470U75+eY6vt+pkGonR6Z0pkMIoN1u88az5yhHUz3b/9hqceP2ejarA8niFJHHSFUVYz0ajUYusqkqxmT3ax9yyCCG95DP5OV5b/Bh5g2bbDabrK6uZsbm5ubwvKHbeoKr6mgAkiQhDMOMoziOmZycZGNjI9cJrbVEUTQQhJfnJN2vqiLSqVqapn1PWygUsjUWGb0Eeet9EHAURUPXHAuAtbbHQbf+lUolNwGNMaOVIAzDDAfysPw43TKwDU/ThmZg+95dlr+8ihFB1QHCc59+gR+WR+bJsTLgFn5kZulXKrducq5UZGbxFrsLP51eBkQEtZagcYmp9z/kr7deR0wH88rKSi4OFAoFarXaaCREAVVKr7zG+jtv4nbi/Yu6H9lEhHq9Pr42lLNnIZjg388/whRLSLmMrVRRYHZ2NneaR27D4qXLTLz48uHLAXNaHMAIxpMTqd3J2nB7m6WlpYwWzM/PH6mGY+NAkiRMT09nHDWbTaIoIo7jI7PQFa8gCEbjgIjgnNsP5Jzb14dBcnxY/XzfJwiCk6lhP/T9xkSEarU6XjUUkUwGuvWv1WpPvg1LpRLFYvFEajdaF7hOmxkzvoDGGNRpPgBJkvDn2kPsGE/rVGm1WkcA2It3ey3m6nf3sTJOALDwIDkYpg+ANGkbbLq1q3y14sb++FAUFEmTJOkP4O/VOyYoNzzFPsFHkC7/t3On+/M/WLRGBEEvBtUAAAAASUVORK5CYII=
' /></div>
<div class='right'>
	<label for='{$id}check' class='help' />Will allow some check on your configuration. Learn more about these options on <a href='#' target='_blank'>the wiki</a>.</label>
	<br/>
	<a class="ormbutton ui-state-default ui-corner-all" href="{$checkUrl}">
		<span class="ui-icon ui-icon-circle-check"></span>
		Check
	</a>
</div>

</form>
