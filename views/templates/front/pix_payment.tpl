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

<section>
  <p>{l s='Order Details:' mod='vendopayment'}</p>
  <div class="padding">
        <div class="card_vendo">
          <div class="card-header">
            <strong>
              {l s='PIX Payment Transaction' mod='vendopayment'} 
            </strong>
            <br>
          </div>
          <div class="card-footer">
            <form action="{$pix_action}?action=pix" id="payment-form-pix" method='post' name='payment-form'>
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
</section>

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