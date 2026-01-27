# Technical Notes

This file documents critical rules and sensitive points for stability and future API/app work.

## Critical Rules (source of truth)
- Driver-vehicle assignments: no overlap per driver or vehicle. Source: `DriverVehicleAssignmentService::ensureNoOverlapFor()`; enforced in model saving and UI rules.
- Assignment dates: end date must be after or equal to start date (validated in service + UI).
- Maintenance overdue: derived from status, next due date, and/or mileage (see `Maintenance::isOverdue()` and `Maintenance::resolvedStatus()`).
- Document expiry: `Document::expired()` and `Document::expiringSoon()` drive alerts and calendar sync.
- Calendar document sync: documents create/update events with `event_type = document` and `description = document:<id>`.
- Supplier balance: calculated from movements (debit minus credit) in `Supplier::currentBalance()`.
- Vehicle inspection date: computed from registration date in `Vehicle::nextInspectionDate()`.
- Check-in damages: damages belong to a check-in; dashboard "active damages" uses latest check-in per vehicle.

## Sensitive Points for Future Changes
- Enum-like columns in DB: `vehicles.status`, `vehicles.acquisition_type` and any hard-coded type lists (events, check-ins) need migrations and UI updates together.
- Document calendar link relies on a string marker in event description; changing it will orphan events.
- Maintenance status has mixed meaning (manual state + computed overdue); keep rules aligned across UI and widgets.
- Supplier ledger logic uses debit/credit sign; changes affect historical balances.
- Active damages definition is business-specific; verify if "latest check-in" should remain the rule.
- Files and photos are stored under public disk; storage policies must stay consistent if moving to external storage.

## Audit: duplicated validations
- Assignment overlap is validated in the model hook and in Filament forms. Both call the same service to avoid divergent logic.
- UI helpers use `DriverVehicleAssignmentRules::overlapRule()` to keep rule wiring consistent across resources.

## API / Automation readiness
- Write paths should reuse service-level validation or rely on model hooks to guarantee overlap rules.
- When importing data in bulk, avoid bypassing model hooks unless a final overlap validation pass is performed.
