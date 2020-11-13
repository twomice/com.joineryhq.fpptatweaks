{*
 At time of writing, a row will be array in this format:
  array (
    'Date_started' => '2020-10-02 00:00:00',
    'Event_Attended' => 'b',
    'Event_Date' => '2020-10-02 00:00:00',
    'CEU_Earned' => 2,
    'CPPT_Level_Completed' => 'Basic',
    'id' => 9923,
    'entity_id' => 7037,
  ),
  *}

{if empty($rows)}
  <p>{ts}This contact has no CPPT History on file.{/ts}
{else}
  <table class="crm-multifield-selector">
    <thead>
      <tr>
        <th>{ts}Event Date{/ts}</th>
        <th>{ts}Event Name{/ts}</th>
        <th>{ts}CEUs{/ts}</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$rows key=key item=row}
      {if $row.Event_Date || $row.Event_Attended || $row.CEU_Earned}
        <tr class="{cycle values="odd-row,even-row"}">
          <td>{$row.Event_Date|crmDate:$dateFormat}</td>
          <td>{$row.Event_Attended}</td>
          <td>{$row.CEU_Earned}</td>
        </tr>
      {/if}
    {/foreach}
    </tbody>
  </table>
{/if}
