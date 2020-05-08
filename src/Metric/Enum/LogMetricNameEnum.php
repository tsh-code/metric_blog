<?php

namespace App\Metric\Enum;

class LogMetricNameEnum
{
    const APP_REQUEST_CALLED_QUANTITY_TOTAL = 'app_request_called_quantity_total';
    const APP_REQUEST_EXECUTION_TIME_SECONDS = 'app_request_execution_time_seconds';
    const APP_REQUEST_DB_QUERY_EXECUTION_TIME_SECONDS = 'app_request_db_query_execution_time_seconds';
    const APP_REQUEST_DB_QUERY_QUANTITY = 'app_request_db_query_quantity';
    const APP_SUB_REQUEST_CALLED_QUANTITY_TOTAL = 'app_sub_request_called_quantity_total';
    const APP_REQUEST_DB_QUERY_TYPE = 'app_request_db_query_type';
}
