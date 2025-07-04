<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\BaseAdminController;

class DashboardController extends BaseAdminController
{
    public function salesMonitoring()
    {
        return $this->loadDataAndView('/admin/dashboard/sales_monitoring','layouts/admin_layout');
    }
    
    public function websiteAnalytics()
    {
        return $this->loadDataAndView('/admin/dashboard/website_analytics','layouts/admin_layout');
    }
    public function cryptocurrency()
    {
        return $this->loadDataAndView('/admin/dashboard/crypto_currency','layouts/admin_layout');
    }
    public function helpdeskManagement()
    {
        return $this->loadDataAndView('/admin/dashboard/helpdesk_management','layouts/admin_layout');
    }
}
