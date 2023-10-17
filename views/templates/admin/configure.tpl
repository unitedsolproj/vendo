{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Vendo Payment Module' mod='vendopayment'}</h3>
	<p>
		<strong>{l s='You can get your Merchant ID and your Shared Secret from Vendo backoffice here ' mod='vendopayment'}</strong><br />
		<a target="_blank" href="https://backoffice.vend-o.com/merchant-merchants/index">https://backoffice.vend-o.com/merchant-merchants/index</a>
		
		<br />
		<br />
		<strong>{l s='Easy technical integration:' mod='vendopayment'}</strong><br />
		<a target="_blank" href="https://vendoservices.com/technical-integration/">https://vendoservices.com/technical-integration/</a>

		<br />
		<br />

		<strong>{l s='Vendo Services Complete Documentation:' mod='vendopayment'}</strong><br />
		<a target="_blank" href="https://docs.vendoservices.com/docs/getting-started">https://docs.vendoservices.com/docs/getting-started</a>

		<br />
		<br />

		<strong>{l s='Vendo Services Success URL:' mod='vendopayment'}</strong><br />
		<a href="#">{$successurl}</a>

		<br />
		<br />

		<strong>{l s='Vendo Services Postback URL:' mod='vendopayment'}</strong><br />
		<a href="#">{$postbackurl}</a>

		<br />
		<br />
	</p>
</div>
