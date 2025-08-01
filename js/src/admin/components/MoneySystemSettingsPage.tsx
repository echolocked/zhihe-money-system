import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import app from 'flarum/admin/app';

export default class MoneySystemSettingsPage extends ExtensionPage {
  content() {
    return (
      <div className="MoneySystemSettingsPage">
        <div className="container">
          <div className="Form">
            {this.buildSettingComponent({
              setting: 'zhihe-money-system.payment_amount',
              type: 'number',
              label: 'Payment amount per discussion view',
              help: 'Amount of money deducted when a user views a discussion for the first time. Set to 0 to disable automatic payments.',
              min: 0,
              step: 1,
            })}

            <div className="Form-group">
              <label>Tag restrictions</label>
              <div className="helpText">
                Set minimum money requirements for specific tags
              </div>
              <p>
                <em>Tag restriction management coming soon</em>
              </p>
            </div>

            {this.submitButton()}
          </div>
        </div>
      </div>
    );
  }
}