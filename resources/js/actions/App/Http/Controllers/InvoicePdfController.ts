import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
const InvoicePdfController = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: InvoicePdfController.url(args, options),
    method: 'get',
})

InvoicePdfController.definition = {
    methods: ["get","head"],
    url: '/invoices/{invoice}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
InvoicePdfController.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { invoice: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            invoice: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        invoice: typeof args.invoice === 'object'
        ? args.invoice.id
        : args.invoice,
    }

    return InvoicePdfController.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
InvoicePdfController.get = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: InvoicePdfController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
InvoicePdfController.head = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: InvoicePdfController.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
const InvoicePdfControllerForm = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: InvoicePdfController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
InvoicePdfControllerForm.get = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: InvoicePdfController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoicePdfController::__invoke
* @see app/Http/Controllers/InvoicePdfController.php:11
* @route '/invoices/{invoice}/pdf'
*/
InvoicePdfControllerForm.head = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: InvoicePdfController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

InvoicePdfController.form = InvoicePdfControllerForm

export default InvoicePdfController