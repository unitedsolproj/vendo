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

<section id="card_aaa">
  <p>{l s='Order Details:' mod='vendopayment'}</p>
  <form action="{$card_action}?action=card" id="payment-form" method='post' name='payment-form'>
  <div class="card_vendo">
    <div class="card-header">
      <strong>
        {l s='Pay By Card' mod='vendopayment'} </strong>
      <br>
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
      <button class="btn btn-primary float-xs-right -xs-down" type="submit">
        {l s='Pay with Card' mod='vendopayment'}
      </button>

    </div>
  </div>
  </form>
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