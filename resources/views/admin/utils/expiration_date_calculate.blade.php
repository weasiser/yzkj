<script>
  $(function() {
    $('.has-many-pes-forms').on('click', '.pes.production_date', function () {
      $(this).attr('type', 'date')
      let beforeDate = $(this)
      $(this).on('change', function(e) {
        console.log(e)
        let complete = function(n){
          return (n>9) ? n : '0' + n;
        }
        let this_production_date = new Date(beforeDate.val())
        console.log(this_production_date)
        this_production_date.setMonth(this_production_date.getMonth() + 18)
        let this_year = this_production_date.getFullYear()
        let this_month = complete(this_production_date.getMonth() + 1)
        let this_day = complete(this_production_date.getDate())
        let this_expiration_date = (this_year+'-'+this_month+'-'+this_day)
        beforeDate.parents('.has-many-pes-form').find('.pes.expiration_date').val(this_expiration_date)
      })
    })
  })
</script>

<style>
  .pes.production_date, .pes.expiration_date {
    width: 150px !important;
  }
</style>
