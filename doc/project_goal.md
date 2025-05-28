# Project Title

Simple Virtual Online ATM System

## Group
* 408850419 郭鎮維
* 413853234 杜利維
* 413857011 陳奕樺

## High-Level Functionalities
* Users can register a new account (self-service sign-up)
* Users can log in with their account and password
* Users can check their account balance
* Users can perform deposit and withdrawal operations
* Users can view recent transaction history

## Example Scenario 1: Checking Balance

* **User**: Wei, a student who wants to check his account balance
* **Goal**: Log in and view current balance

### Flow
1.  Wei opens the ATM website and logs in with his account and password
2.  The system checks if his account and password are correct by comparing them to stored data
3.  After logging in successfully, Wei clicks “Check Balance”
4.  The system searches for Wei’s account balance in the database
5.  The balance is shown on the screen, for example: “Your current balance is $3,500”

### PHP Logic
* Handle user login
* Process balance check request
* Display the balance on the web page

### Database Operations
* Verify that the entered account and password match a valid user
* Look up the balance value linked to that user account

## Example Scenario 2: Withdrawal

* **User**: Fang, an office worker who wants to withdraw $500
* **Goal**: Log in and perform a withdrawal

### Flow
1.  Fang opens the ATM website and logs into her account
2.  She clicks on "Withdraw" and enters the amount: $500
3.  The system checks whether her account has at least $500 available
4.  If the balance is sufficient, the system subtracts $500 from her account
5.  A new transaction record is saved for future history
6.  The system shows the result: “Withdrawal successful. Remaining balance: $1,200”

### PHP Logic
* Handle withdrawal form input
* Check if the user has enough balance
* Update the balance and record the transaction
* Show the updated balance on the page

### Database Operations
* Check the current balance of the user
* Update the account with the new balance
* Store a new transaction record in the database (e.g., type: withdrawal, amount: $500)
