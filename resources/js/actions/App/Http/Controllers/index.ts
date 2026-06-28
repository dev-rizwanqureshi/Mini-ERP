import DashboardController from './DashboardController'
import CustomerController from './CustomerController'
import ProductController from './ProductController'
import InvoiceController from './InvoiceController'
import InvoicePdfController from './InvoicePdfController'
import PaymentController from './PaymentController'
import ReportController from './ReportController'
import SettingController from './SettingController'
import UserController from './UserController'
import Settings from './Settings'

const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    CustomerController: Object.assign(CustomerController, CustomerController),
    ProductController: Object.assign(ProductController, ProductController),
    InvoiceController: Object.assign(InvoiceController, InvoiceController),
    InvoicePdfController: Object.assign(InvoicePdfController, InvoicePdfController),
    PaymentController: Object.assign(PaymentController, PaymentController),
    ReportController: Object.assign(ReportController, ReportController),
    SettingController: Object.assign(SettingController, SettingController),
    UserController: Object.assign(UserController, UserController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers