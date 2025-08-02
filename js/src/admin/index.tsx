import app from 'flarum/admin/app';

app.initializers.add('zhihe-money-system', () => {
  app.extensionData
    .for('zhihe-money-system')
    .registerSetting({
      setting: 'zhihe-money-system.payment_amount',
      label: app.translator.trans('zhihe-money-system.admin.settings.payment_amount_label'),
      help: app.translator.trans('zhihe-money-system.admin.settings.payment_amount_help'),
      type: 'number',
      min: 0,
      step: 1,
      default: 1,
    })
    .registerSetting({
      setting: 'zhihe-money-system.initial_money',
      label: app.translator.trans('zhihe-money-system.admin.settings.initial_money_label'),
      help: app.translator.trans('zhihe-money-system.admin.settings.initial_money_help'),
      type: 'number',
      min: 0,
      step: 0.01,
      default: 0,
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('zhihe-money-system.admin.settings.tag_restrictions_label')}</label>
          <div className="helpText">{app.translator.trans('zhihe-money-system.admin.settings.tag_restrictions_help')}</div>
          <p>
            <em>{app.translator.trans('zhihe-money-system.admin.settings.tag_restrictions_coming_soon')}</em>
          </p>
        </div>
      );
    })
    .registerPermission(
      {
        icon: 'fas fa-eye-slash',
        label: app.translator.trans('zhihe-money-system.admin.permissions.view_without_payment_label'),
        permission: 'discussion.viewWithoutPayment',
      },
      'moderate'
    );
});