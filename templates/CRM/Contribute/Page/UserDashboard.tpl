{*
 * Copied and modified from civicrm core template templates/CRM/Contribute/Page/UserDashboard.tpl
 * as of civicrm version 5.70.1
 *
 * Template modified to implement changes in "Contributions" section of user dashboard,
 * per https://joinery.freshdesk.com/a/tickets/1418 (specifically, significant changes
 * to which columns are displayed, in comment from MJ Chwalik dated "Thu, 4 Apr 2024 at 12:49 PM")
 *}
{crmRegion name="crm-contribute-userdashboard-pre"}
{/crmRegion}
<div class="view-content">
    {if $contribute_rows}
        {strip}
            <table class="selector">
                <tr class="columnheader">
                    <th>{ts}Source{/ts}</th>
                    <th>{ts}Invoice #{/ts}</th>
                    <th>{ts}Contribution Date{/ts}</th>
                    <th>{ts}Balance{/ts}</th>
                    <th>{ts}Status{/ts}</th>
                    {if $isIncludeInvoiceLinks}
                      <th></th>
                    {/if}
                    <th></th>
                </tr>

                {foreach from=$contribute_rows item=row}
                    <tr id='rowid{$row.id}'
                        class="{cycle values="odd-row,even-row"}{if !empty($row.cancel_date)} disabled{/if}">
                        <td>{$row.source|escape|smarty:nodefaults}</td>
                        <td>{$row.invoice_number|escape|smarty:nodefaults}</td>
                        <td>{$row.receive_date|truncate:10:''|crmDate}</td>
                        <td>{$row.balance_amount|crmMoney:$row.currency}</td>
                        <td>{$row.contribution_status|escape|smarty:nodefaults}</td>
                        {if $isIncludeInvoiceLinks}
                          <td>
                            {* @todo Instead of this tpl handling assign actions as an array attached the row, iterate through - will better accomodate extension overrides and competition for scarce real estate on this page*}
                            {assign var='id' value=$row.id}
                            {assign var='contact_id' value=$row.contact_id}
                            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id"}
                            {if call_user_func(array('CRM_Core_Permission','check'), 'view my invoices') OR call_user_func(array('CRM_Core_Permission','check'), 'access CiviContribute')}
                                <a class="button no-popup nowrap"
                                   href="{crmURL p='civicrm/contribute/invoice' q=$urlParams}">
                                    <i class="crm-i fa-download" aria-hidden="true"></i>
                                    {if empty($row.contribution_status_name) || (!empty($row.contribution_status_name) && $row.contribution_status_name != 'Refunded' && $row.contribution_status_name != 'Cancelled')}
                                        <span>{ts}Download Invoice{/ts}</span>
                                    {else}
                                        <span>{ts}Download Invoice and Credit Note{/ts}</span>
                                    {/if}
                                </a>
                            {/if}
                          </td>
                        {/if}
                        {if !empty($row.buttons)}
                        <td>
                        {foreach from=$row.buttons item=button}
                          <a class="{$button.class}" href="{$button.url}"><span class='nowrap'>{$button.label}</span></a>
                        {/foreach}
                        </td>
                        {/if}
                    </tr>
                {/foreach}
            </table>
        {/strip}
        {if !empty($contributionSummary.total) and $contributionSummary.total.count gt 12}
            {ts}Contact us for information about contributions prior to those listed above.{/ts}
        {/if}
    {else}
        <div class="messages status no-popup">
            {icon icon="fa-info-circle"}{/icon}
            {ts}There are no contributions on record for you.{/ts}
        </div>
    {/if}

    {if !empty($soft_credit_contributions)}
        {strip}
            <div class="help">
                {ts}Contributions made in your honor{/ts}:
            </div>
            <table class="selector">
                <tr class="columnheader">
                    <th>{ts}Contributor{/ts}</th>
                    <th>{ts}Type{/ts}</th>
                    <th>{ts}Source{/ts}</th>
                    <th>{ts}Invoice #{/ts}</th>
                    <th>{ts}Contribution Date{/ts}</th>
                    <th>{ts}Balance{/ts}</th>
                    <th>{ts}Status{/ts}</th>
                </tr>
                {foreach from=$soft_credit_contributions item=row}
                    <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
                        <td>{$row.display_name|escape|smarty:nodefaults}</td>
                        <td>{$row.soft_credit_type|escape|smarty:nodefaults}</td>
                        <td>{$row.source|escape|smarty:nodefaults}</td>
                        <td>{$row.invoice_number|escape|smarty:nodefaults}</td>
                        <td>{$row.receive_date|truncate:10:''|crmDate}</td>
                        <td>{$row.contribution_status|escape|smarty:nodefaults}</td>
                    </tr>
                {/foreach}
            </table>
        {/strip}
    {/if}

        {if !empty($recurRows)}
            {strip}
                <div><label>{ts}Recurring Contribution(s){/ts}</label></div>
                <table class="selector">
                    <tr class="columnheader">
                        <th>{ts}Terms:{/ts}</th>
                        <th>{ts}Status{/ts}</th>
                        <th>{ts}Installments{/ts}</th>
                        <th>{ts}Created{/ts}</th>
                        <th></th>
                    </tr>
                    {foreach from=$recurRows item=row}
                        <tr class="{cycle values="odd-row,even-row"}">
                            <td><label>{$row.amount|crmMoney}</label>
                                every {$row.frequency_interval} {$row.frequency_unit} for {$row.installments} installments
                            </td>
                            <td>{$row.recur_status|escape|smarty:nodefaults}</td>
                            <td>{if $row.completed}<a href="{$row.link}">{$row.completed}
                                    /{$row.installments}</a>
                                {else}0/{$row.installments} {/if}</td>
                            <td>{$row.create_date|crmDate}</td>
                            <td>{$row.action|replace:'xx':$row.id}</td>
                        </tr>
                    {/foreach}
                </table>
            {/strip}
        {/if}

</div>
{crmRegion name="crm-contribute-userdashboard-post"}
{/crmRegion}
