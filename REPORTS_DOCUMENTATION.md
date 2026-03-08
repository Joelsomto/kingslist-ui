# Dispatch & Delivery Report System - Documentation

## Overview
This documentation covers the new comprehensive reporting system for Kingslist that shows who received and who didn't receive dispatch messages, with detailed analytics for any time period.

---

## 📋 Report Pages

### 1. **Main Dashboard** - `dispatch-delivery-reports.php`
**Purpose:** Central hub for all reporting and analytics

**Features:**
- Quick statistics cards showing:
  - Total campaigns for the current year
  - Number of messages delivered
  - Number of messages not delivered (pending/failed)
  - Overall delivery success rate
- Direct links to specialized reports
- Recent campaigns table showing latest activities
- Year selection for quick navigation
- Export functionality

**How to Access:**
```
http://your-domain/dispatch-delivery-reports.php
```

**Key Sections:**
- Quick Stats Overview (4 cards with key metrics)
- Report Type Selection (2 main report types)
- Available Years Filter
- Recent Campaigns list

---

### 2. **Yearly Analytics Report** - `yearly-delivery-report.php`
**Purpose:** Comprehensive year-over-year dispatch and delivery statistics

**Features:**
- Year selector dropdown
- Summary statistics:
  - Total campaigns dispatched
  - Total messages delivered (success)
  - Total messages not delivered
  - Overall delivery rate percentage
- Monthly trend chart showing:
  - Messages sent per month
  - Messages delivered per month
  - Visual trend analysis
- Detailed campaign table with:
  - Campaign name
  - Total recipients
  - Delivered count
  - Not delivered count
  - Delivery percentage with progress bar
  - Sent date
  - Status indicator (Completed/Partial/Pending)
- Export options:
  - CSV export
  - JSON export
  - Print functionality

**How to Access:**
```
http://your-domain/yearly-delivery-report.php
http://your-domain/yearly-delivery-report.php?year=2025
```

**URL Parameters:**
- `year` - Year to filter by (default: current year)

**Example Usage:**
```
View 2024 report: yearly-delivery-report.php?year=2024
View current year: yearly-delivery-report.php
```

---

### 3. **Recipient Delivery Details** - `recipient-delivery-report.php`
**Purpose:** Show exactly which individuals received or didn't receive specific campaign messages

**Features:**
- Campaign selector showing all campaigns with dates
- Status filter:
  - All Recipients
  - Delivered Only (successful deliveries)
  - Not Delivered (pending/failed)
- Year selector for historical data
- Detailed statistics:
  - Total recipients in campaign
  - Number delivered (with green badge)
  - Number failed (with red badge)
  - Success rate percentage
- Recipients table showing:
  - Username/Handle
  - Recipient Name
  - Delivery Status (✓ Delivered, ✗ Failed, ⏳ Pending)
  - Delivery timestamp (when delivered)
  - Detailed status message
- CSV export functionality

**How to Access:**
```
http://your-domain/recipient-delivery-report.php
http://your-domain/recipient-delivery-report.php?year=2025&campaign=123
http://your-domain/recipient-delivery-report.php?year=2025&status=received
```

**URL Parameters:**
- `year` - Year to filter by (default: current year)
- `campaign` - Campaign ID to view details for
- `status` - Filter by status: `all`, `received`, or `not_received`

**Example Usage:**
```
View campaign 123: recipient-delivery-report.php?campaign=123
View only delivered: recipient-delivery-report.php?campaign=123&status=received
View only failed: recipient-delivery-report.php?campaign=123&status=not_received
View 2024 data: recipient-delivery-report.php?year=2024
```

---

### 4. **Campaign Dispatch Log** - `dispatch-report.php` (Existing)
**Purpose:** View original dispatch log for a specific campaign

**Complements the new system** with detailed per-message delivery tracking

**How to Access:**
```
http://your-domain/dispatch-report.php?id=CAMPAIGN_ID
```

---

## 🎯 Use Cases

### Use Case 1: "Who received our dispatch this year?"
**Steps:**
1. Go to `dispatch-delivery-reports.php` (main dashboard)
2. Click "Yearly Analytics" report
3. View the summary stats showing total delivered vs not delivered
4. Click on a campaign to see details in the Campaign Details table

**What you'll see:**
- Total recipients reached across all campaigns
- Success rate percentage
- Campaign-by-campaign breakdown

---

### Use Case 2: "Which specific people didn't receive the message?"
**Steps:**
1. Go to `recipient-delivery-report.php`
2. Select the campaign from dropdown
3. Change Status filter to "Not Delivered"
4. Review the recipients list

**What you'll see:**
- Names and handles of all recipients who didn't receive
- Reason for failure (Failed, Pending, etc.)
- When delivery was attempted

---

### Use Case 3: "Monthly dispatch trends"
**Steps:**
1. Go to `yearly-delivery-report.php`
2. Select year from dropdown
3. Scroll to "Monthly Trend" chart

**What you'll see:**
- Bar chart showing:
  - Messages sent each month
  - Messages delivered each month
  - Visual comparison of trend over time

---

### Use Case 4: "Generate report for my boss"
**Steps:**
1. Go to any report page
2. Click the "Export" button
3. Choose format: CSV or JSON
4. File downloads to your computer

**Formats Available:**
- **CSV:** Open in Excel/Sheets for analysis
- **JSON:** For programmatic use or integration
- **Print:** Use browser print function for PDF

---

## 📊 Understanding the Data

### Status Meanings

#### Message Statuses:
- **✓ Delivered** - Message was successfully sent and received
- **✗ Failed** - Delivery attempt failed (network, invalid recipient, etc.)
- **⏳ Pending** - Message is queued/waiting to be sent
- **Completed** - All recipients received the campaign (100% rate)
- **Partial** - Some but not all recipients received
- **Pending** - Campaign just created, dispatch not yet started

### Metrics Explained

**Delivery Rate:** 
```
(Number Delivered / Total Attempted) × 100
```
Shows the percentage of messages successfully delivered.

**Not Delivered:**
```
Total Attempted - Number Delivered
```
Includes both failed and pending messages.

---

## 🔧 Technical Details

### Database Tables Used
1. **dispatch_msg** - Campaign/message metadata
2. **message_dispatch_log** - Individual recipient delivery records
3. **namelist** - List names/titles
4. **list** - Individual recipients
5. **users** - User account information

### Key Database Fields
```sql
dispatch_msg:
- dmsg_id: Campaign unique ID
- title: Campaign name
- dispatch_count: Total recipients in campaign
- created_at: When campaign was sent
- user_id: User who sent campaign

message_dispatch_log:
- dmsg_id: Which campaign
- list_id: Which recipient
- status: 'success', 'failed', 'pending'
- created_at: Delivery timestamp

list:
- list_id: Recipient unique ID
- kc_username: Recipient's username/handle
- list_name: Recipient's display name
```

---

## 💡 Best Practices

### For Regular Reporting
1. **Monthly Review:** Visit `yearly-delivery-report.php` monthly to track trends
2. **Campaign Review:** After each dispatch, check `recipient-delivery-report.php` to see who received
3. **Problem Analysis:** Use "Not Delivered" filter to identify delivery issues
4. **Documentation:** Export reports as CSV monthly for record-keeping

### For Compliance/Documentation
1. Use the **Print** function to create PDF reports
2. Export as **CSV** for archival in spreadsheets
3. Include generated date and user information
4. Keep exports for audit trail

### For Troubleshooting
1. If delivery rate is low, check the "Not Delivered" list
2. Look at failure patterns in the monthly trends
3. Check if recipients are marked as failed vs pending
4. Compare success rate between different campaigns

---

## 🎨 Features Summary

### Dashboard (`dispatch-delivery-reports.php`)
✅ Quick statistics overview  
✅ Report type selection  
✅ Recent campaigns list  
✅ Year navigation  
✅ CSV export of summary  

### Yearly Report (`yearly-delivery-report.php`)
✅ Year selector  
✅ Monthly trend visualization  
✅ Campaign-level details  
✅ Delivery rate tracking  
✅ CSV/JSON export  
✅ Print support  

### Recipient Report (`recipient-delivery-report.php`)
✅ Campaign filter  
✅ Status filter (received/not received)  
✅ Year selector  
✅ Individual recipient details  
✅ Delivery timestamps  
✅ CSV export  

---

## 📱 Mobile Support
All report pages are fully responsive and work on:
- ✅ Desktop computers
- ✅ Tablets
- ✅ Mobile phones
- ✅ Print-friendly layouts

---

## ❓ FAQ

**Q: Where do I start?**
A: Visit `dispatch-delivery-reports.php` - the main dashboard with all options.

**Q: How far back do reports go?**
A: Reports show all campaigns from any year that you have data for. Select year in dropdown.

**Q: Can I share these reports?**
A: Yes, export as CSV/JSON or print to PDF for sharing.

**Q: What does "Not Delivered" include?**
A: Both "Failed" (delivery failed) and "Pending" (not yet sent) statuses.

**Q: How often is data updated?**
A: Data updates in real-time as dispatch operations complete.

**Q: Can I filter by specific date range?**
A: Currently year-based filtering is available. For specific dates, use exported CSV data.

---

## 🔄 Integration with Existing Pages

These reports work seamlessly with:
- **dispatch.php** - Create new dispatch campaigns
- **dispatch-report.php** - View individual campaign logs
- **list.php** - View recipients in a list
- **lists.php** - Manage all your lists

---

## 📞 Support

For issues or questions:
1. Check the recipient details report to verify data
2. Ensure you have proper year/campaign selected
3. Verify database connection is working
4. Check that dispatch records exist for selected period

---

## 🔐 Permissions

Reports respect user session authentication via:
- `$_SESSION['user_id']` - Only shows current user's data
- All queries filtered by user_id
- No access to other users' dispatch data

---

## Version Information
- **Created:** 2026
- **System:** Kingslist v2 Online
- **Reports:** Yearly Delivery, Recipient Details, Dispatch Analytics
- **Data Source:** Dispatch Message Log System

---

**Last Updated:** March 2026
