<script>
  $(function() {
    $('.fields-group').on('change', '#production_date', function () {
      let quality_guarantee_period = parseInt($('#quality_guarantee_period').text())
      let beforeDate = $(this)
      let complete = function(n){
        return (n>9) ? n : '0' + n;
      }
      let this_production_date = new Date(beforeDate.val())
      this_production_date.setMonth(this_production_date.getMonth() + quality_guarantee_period)
      let this_year = this_production_date.getFullYear()
      let this_month = complete(this_production_date.getMonth() + 1)
      let this_day = complete(this_production_date.getDate())
      let this_expiration_date = (this_year+'-'+this_month+'-'+this_day)
      beforeDate.parents('.fields-group').find('#expiration_date').val(this_expiration_date)
    })
  })
</script>

<style>
  .quality_guarantee_period_note {
    /*display: none;*/
    margin-left: 50px;
  }
</style>
