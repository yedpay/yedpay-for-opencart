# YedPay OpenCart Extension

## Description

An OpenCart extension to settle online payments using Yedpay.

### Prerequisites

1. Set up *OpenCart*.
2. Log into *OpenCart* as "Admin" *(https://your_host_name/admin)*.
3. Navigate to "Currencies" *(System > Localisation > Currencies)*.
4. Click the "+" button on top right to add currency.
5. Input the followings:

    | Field          | Value            |
    | -------------- | ---------------- |
    | Currency Title | Hong Kong Dollar |
    | Code           | HKD              |
    | Decimal Places | 2                |
    | Status         | Enable           |

6. Click the "Save" button.
7. Navigate to "Settings" *(System > Settings)*.
8. Click the "Edit" button in the Action column of your store.
9. Select the "Local" Tab.
10. Select "Hong Kong Dollar" for "Currency".
11. Click the "Save" button.

### Install from zip

1. Download the yedpay.ocmod.zip from [OpenCart marketplace](https://www.opencart.com/index.php?route=marketplace/extension).
2. In *OpenCart Admin Page*, navigate to "Installer" *(Extensions > Installer)*.
3. Upload the zip file.

### Install from source

1. Download/clone this repository. Please note that your mush have composer and zip installed.
2. `bash bin/build.sh`.
3. In *OpenCart Admin Page*, navigate to "Installer" *(Extensions > Installer)*.
4. Upload the zip file.

### Configuration

1. In *OpenCart Admin Page*, navigate to "Extension" (*Extension > Extension)*.
2. Select "Payments" for "Choose Extension type".
3. Locate Yedpay and click the green "Install" button in the "Action" column.
4. After finish installing, click the blue "Edit" button in the "Action" column.
5. Enter "Sign Key" and "API Key". *(Refer to [Key Materials](#key-materials) to obtain Sign Key and API Key)*
6. For "Test Mode", select "Sandbox" for testing, "Production" for production.
7. Change the status to "Enabled".
8. Click the "Save" button.

### Key Materials

#### Sign Key

1. Log into [YedPay's Merchant Portal](https://merchant.yedpay.com) as owner.
2. Navigate to "App Keys" *(Admin > App Keys)*.
3. If Sign Key is absent, click the "Generate" button.
4. Copy the "Sign Key" shown.

#### API Key

1. Log into [YedPay's Merchant Portal](https://merchant.yedpay.com) as owner.
2. Navigate to "App Keys" *(Admin > App Keys)*.
3. In the "API Keys" section, click the "Add" button.
4. Enter "Key Name" and select your online store, then click the "Add" button again to get a new API Key.
5. Copy the "API Key" shown. (The API Key will only be displayed once, save it immediately!)
