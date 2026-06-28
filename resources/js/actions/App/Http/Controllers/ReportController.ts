import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
export const sales = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sales.url(options),
    method: 'get',
})

sales.definition = {
    methods: ["get","head"],
    url: '/reports/sales',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
sales.url = (options?: RouteQueryOptions) => {
    return sales.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
sales.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sales.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
sales.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sales.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
const salesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sales.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
salesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sales.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::sales
* @see app/Http/Controllers/ReportController.php:13
* @route '/reports/sales'
*/
salesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sales.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

sales.form = salesForm

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
export const customers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: customers.url(options),
    method: 'get',
})

customers.definition = {
    methods: ["get","head"],
    url: '/reports/customers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
customers.url = (options?: RouteQueryOptions) => {
    return customers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
customers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: customers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
customers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: customers.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
const customersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: customers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
customersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: customers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::customers
* @see app/Http/Controllers/ReportController.php:23
* @route '/reports/customers'
*/
customersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: customers.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

customers.form = customersForm

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
export const payments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payments.url(options),
    method: 'get',
})

payments.definition = {
    methods: ["get","head"],
    url: '/reports/payments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
payments.url = (options?: RouteQueryOptions) => {
    return payments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
payments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
payments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: payments.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
const paymentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: payments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
paymentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: payments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::payments
* @see app/Http/Controllers/ReportController.php:33
* @route '/reports/payments'
*/
paymentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: payments.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

payments.form = paymentsForm

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
export const outstanding = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: outstanding.url(options),
    method: 'get',
})

outstanding.definition = {
    methods: ["get","head"],
    url: '/reports/outstanding',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
outstanding.url = (options?: RouteQueryOptions) => {
    return outstanding.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
outstanding.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: outstanding.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
outstanding.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: outstanding.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
const outstandingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: outstanding.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
outstandingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: outstanding.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::outstanding
* @see app/Http/Controllers/ReportController.php:43
* @route '/reports/outstanding'
*/
outstandingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: outstanding.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

outstanding.form = outstandingForm

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
export const exportMethod = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/reports/export/{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
exportMethod.url = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { type: args }
    }

    if (Array.isArray(args)) {
        args = {
            type: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        type: args.type,
    }

    return exportMethod.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
exportMethod.get = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
exportMethod.head = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
const exportMethodForm = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
exportMethodForm.get = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:53
* @route '/reports/export/{type}'
*/
exportMethodForm.head = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

const ReportController = { sales, customers, payments, outstanding, exportMethod, export: exportMethod }

export default ReportController