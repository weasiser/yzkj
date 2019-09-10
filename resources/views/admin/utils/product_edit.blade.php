<script>
  $(function() {
    $('.has-many-productpeswithoutsoldoutchecked-forms').on('change', '.productpeswithoutsoldoutchecked.production_date', function () {
      let quality_guarantee_period = parseInt($('#quality_guarantee_period').val())
      if (!quality_guarantee_period) {
        Sweetalert2('请先填写商品保质期，才能自动生成有效日期')
      }
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
      beforeDate.parents('.has-many-productpeswithoutsoldoutchecked-forms').find('.productpeswithoutsoldoutchecked.expiration_date').val(this_expiration_date)
    })
  })
</script>

<style>
</style>
