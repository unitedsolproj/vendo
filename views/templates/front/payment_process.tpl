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

{extends file=$layout}
{block name='content'}
	{if isset($error_code) && $error_code == 1}
		{if $pay_status == 2}
			<div class="notifications-container container">
			  <article class="alert alert-danger" role="alert" data-alert="danger">
			    <ul>
			      <li>{l s='Payment transaction failed. Please try again.' mod='vendopayment'}</li>
			    </ul>
			  </article>
			  <a href="{$order_page_url}" style="margin-bottom: 10px;" class="btn btn-primary">
				 {l s='Try Again' mod='vendopayment'}
			  </a>
			</div>
		{else}
			<div class="notifications-container container">
			  <article class="alert alert-danger" role="alert" data-alert="danger">
			    <ul>
			      <li>{l s='Payment transaction failed. Please try again.' mod='vendopayment'}</li>
			    </ul>
			  </article>
			  <a href="{$order_page_url}" style="margin-bottom: 10px;" class="btn btn-primary">
				 {l s='Try Again' mod='vendopayment'}
			  </a>
			</div>
		{/if}
	{else}

	<div class="row">
	  <div class="col-lg-1"></div>
	  <div class="col-sm-12 col-lg-6">
	    <form action="{$action}?action=validate" id="payment-form" method='post' name='payment-form'>
	      <input name="account_title" type="hidden" value="{$account_title}" />
	      <input name="success_url" type="hidden" value="" />
	      <input name="back_url" type="hidden" value="{$back_url}" />
	      <input name="account_desc" type="hidden" value="{$account_desc}" />
	      <input name="secret_key_test" type="hidden" value="{$secret_key_test}" />
	      <input name="id_customer" type="hidden" value="{$id_customer}" />
	      <input name="secret_key" type="hidden" value="{$secret_key}" />
	      <input name="merchant_id" type="hidden" value="{$merchant_id}" />
	      <input name="site_id" type="hidden" value="{$site_id}" />
	      <input name="total_to_pay" type="hidden" value="{$total_to_pay}" />
	      <input name="customer_fname" type="hidden" value="{$customer_fname}" />
	      <input name="customer_lname" type="hidden" value="{$customer_lname}" />
	      <input name="customer_email" type="hidden" value="{$customer_email}" />
	      <input name="id_language" type="hidden" value="{$id_language}" />
	      <input name="language_code" type="hidden" value="{$language_code}" />
	      <input name="customer_city" type="hidden" value="{$customer_city}" />
	      <input name="customer_country" type="hidden" value="{$customer_country}" />
	      <input name="customer_postcode" type="hidden" value="{$customer_postcode}" />
	      <input name="country_code" type="hidden" value="{$country_code}" />
	      <input name="customer_phone" type="hidden" value="{$customer_phone}" />
	      <input name="customer_ip" type="hidden" value="{$customer_ip}" />
	      <div class="padding">
	        <div class="card_vendo">
	          <div class="card-header">
	            <strong>
	              <img src="{$logo}" loading="lazy">{l s='Vendo Credit Card Service' mod='vendopayment'} </strong>
	            <br>
	            <small>{l s='Enter your card details' mod='vendopayment'}</small>
	          </div>
	          <div class="card_body_vendo">
	            <div class="row">
	              <div class="col-sm-12">
	                <div class="form-group">
	                  <label for="name">{l s='Name' mod='vendopayment'}</label>
	                  <input required="required" class="form-control" name="name" id="name" type="text" placeholder="Enter your name">
	                </div>
	              </div>
	            </div>
	            <div class="row">
	              <div class="col-sm-12">
	                <div class="form-group">
	                  <label for="ccnumber">{l s='Credit Card Number' mod='vendopayment'}</label>
	                  <div class="input-group">
	                    <input required="required" name="card_number" id="card_number" maxlength="16" class="form-control" type="text" placeholder="0000 0000 0000 0000">
	                    <div class="input-group-append">
	                      <span class="input-group-text">
	                        <i class="mdi mdi-credit-card"></i>
	                      </span>
	                    </div>
	                  </div>
	                </div>
	              </div>
	            </div>
	            <div class="row">
	              <div class="form-group col-sm-6">
	                <label for="ccmonth">{l s='Exp Month' mod='vendopayment'}</label>
	                <select class="form-control" name="exp_month" id="exp_month">
	                  <option value="01">1</option>
	                  <option value="02">2</option>
	                  <option value="03">3</option>
	                  <option value="04">4</option>
	                  <option value="05">5</option>
	                  <option value="06">6</option>
	                  <option value="07">7</option>
	                  <option value="08">8</option>
	                  <option value="09">9</option>
	                  <option value="10">10</option>
	                  <option value="11">11</option>
	                  <option value="12">12</option>
	                </select>
	              </div>
	              <div class="form-group col-sm-6">
	                <label for="ccyear">{l s='Exp Year' mod='vendopayment'}</label>
	                <select class="form-control" name="exp_year" id="exp_year">
	                  <option value="2023">2023</option>
	                  <option value="2024">2024</option>
	                  <option value="2025">2025</option>
	                  <option value="2026">2026</option>
	                  <option value="2027">2027</option>
	                  <option value="2028">2028</option>
	                  <option value="2029">2029</option>
	                  <option value="2030">2030</option>
	                  <option value="2031">2031</option>
	                  <option value="2032">2032</option>
	                  <option value="2033">2033</option>
	                  <option value="2034">2034</option>
	                  <option value="2035">2035</option>
	                </select>
	              </div>
	              <div class="col-sm-12">
	                <div class="form-group">
	                  <label for="cvv">{l s='CVV/CVC' mod='vendopayment'}</label>
	                  <input required="required" maxlength="3" class="form-control" id="card_cvv" name="card_cvv" type="text" placeholder="123">
	                </div>
	              </div>
	            </div>
	          </div>
	          <div class="card-footer">
	            <button class="btn btn-sm btn-danger" type="reset">
	              <i class="mdi mdi-lock-reset"></i> {l s='Reset' mod='vendopayment'} </button>
	            <button style="float: right;" class="btn btn-sm btn-success float-right" type="submit">
	              <i class="mdi mdi-gamepad-circle"></i> {l s='Continue' mod='vendopayment'} </button>
	          </div>
	        </div>
	      </div>
	    </form>
	  </div>

	  <div class="col-lg-4">
	    <div class="padding">
	      <div class="card_vendo">
	        <div class="card-header">
	          <strong>
	            <img src="{$logo}" loading="lazy">
	            {l s='Vendo Crypto Currency Service ' mod='vendopayment'} 
	        	</strong>
	          <br>
	        </div>
	        <div class="card-footer">
	          <p> {l s='We accept payments in cryptocurrency for your convenience' mod='vendopayment'} </p>
	          
	          <a class="btn btn-primary float-xs-right -xs-down" href="{$action}?action=crypto">{l s='Pay with Cryptocurrency' mod='vendopayment'} </a>

	        </div>
	      </div>
	    </div>
	    <br>
	    <div class="padding">
	      <div class="card_vendo">
	        <div class="card-header">
	          <strong>
	            <img src="{$logo}" loading="lazy">
	            {l s='Vendo PIX Payment Transaction' mod='vendopayment'} 
	        	</strong>
	          <br>
	        </div>
	        <div class="card-footer">
	        	<form action="{$action}?action=pix" id="payment-form-pix" method='post' name='payment-form'>
	          <p> {l s='Experience seamless and lightning-fast payments with Vendo Payment Pix Service' mod='vendopayment'} </p>
	          <div class="row">
	              <div class="col-sm-12">
	                <div class="form-group">
	                  <label for="name">{l s='CPF - Cadastro de Pessoas Físicas - Natural Persons Register' mod='vendopayment'}</label>
	                  <input required required="required" class="form-control" name="cpf" id="cpf" type="text" placeholder="Enter CPF - Ex 723.785.048-29">
	                </div>
	              </div>
	            </div>
	            <button class="btn btn-primary float-xs-right -xs-down" type="submit">
	              {l s='Pay with Pix' mod='vendopayment'}
	          	</button>
	      	</form>
	        </div>
	      </div>
	    </div>
	    <br>
	  </div>
	  <div class="col-lg-1"></div>
	</div>
	
	<style type="text/css">
	.padding{
	}
	.card_vendo {
	    margin-bottom: 1.5rem;
	} 

	.card_vendo {
	    position: relative;
	    display: -ms-flexbox;
	    display: flex;
	    -ms-flex-direction: column;
	    flex-direction: column;
	    min-width: 0;
	    word-wrap: break-word;
	    background-color: #fff;
	    background-clip: border-box;
	    border: 1px solid #c8ced3;
	    border-radius: .25rem;
	}

	.card-header:first-child {
	    border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
	}

	.card-header {
	    padding: .75rem 1.25rem;
	    margin-bottom: 0;
	    background-color: #f0f3f5;
	    border-bottom: 1px solid #c8ced3;
	}

	.card_body_vendo {
	    flex: 1 1 auto;
	    padding: 1.25rem;
	}

	.form-control:focus {
	    color: #5c6873;
	    background-color: #fff;
	    border-color: #c8ced3 !important;
	    outline: 0;
	    box-shadow: 0 0 0 #F44336;
	}
	</style>

	<script type="text/javascript">
		var card_number = document.getElementById('card_number');
        card_number.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16);
            }
        });

        var card_cvv = document.getElementById('card_cvv');
        card_cvv.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 3) {
                this.value = this.value.slice(0, 3);
            }
        });
        
	</script>
	{/if}
{/block}
