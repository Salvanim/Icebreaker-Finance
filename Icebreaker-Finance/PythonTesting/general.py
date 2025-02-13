from databaseManagement import DatabaseManager
from dataVisualizationGenerator import definePlot
from imageProcessor import ImageProcessor
import sys
import inspect
from datetime import date

monthList = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

def get_class_methods(cls):
    methods = inspect.getmembers(cls, predicate=inspect.isfunction)
    return [name for name, _ in methods if name != '__init__']

def libraryMethodsDictionary():
    return {
        "DatabaseManager": get_class_methods(DatabaseManager),
        "definePlot": str(get_class_methods(definePlot) if inspect.isclass(definePlot) else "Function"),
        "ImageProcessor": str(get_class_methods(ImageProcessor))
    }

def getPlot(tableName):
    db = DatabaseManager("mysql.neit.edu", "5500", "capstone_202520_winteriscoming", "Winteriscoming", "capstone_202520_winteriscoming")
    columns = db.execute(f"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{tableName}';")
    table = db.viewTable(tableName)
    print(table)
    return definePlot(table, columns)

def generateBarGraph(debtID, userID):
    db = DatabaseManager("mysql.neit.edu", "5500", "capstone_202520_winteriscoming", "Winteriscoming", "capstone_202520_winteriscoming")
    result = db.execute(f"SELECT amount_owed, interest_rate, min_payment FROM debt_lookup WHERE debt_id = {debtID} AND user_id = {userID};")
    
    if not result:
        print("No data found for the given debt ID and user ID.")
        return
    
    amount, rate, minPayment = result[0]
    payoffDate, months = calculatePayoffDate(amount, rate, minPayment)
    debtAmounts = getDebtAmountsEachMonth(amount, rate, minPayment, months)
    debtMonths = monthFromNow(months)
    return debtMonths, debtAmounts

def getDebtAmountsEachMonth(amount, rate, minPayment, amountOfMonths):
    balance = amount
    debtAmounts = []
    monthlyRate = (rate / 100) / 12
    for _ in range(amountOfMonths):
        balance = balance * (1 + monthlyRate) - minPayment
        if balance < 0:
            balance = 0
        debtAmounts.append(balance)
    return debtAmounts

def monthFromNow(monthDistance):
    months = []
    today = date.today()
    for i in range(monthDistance):
        year_offset = (today.month - 1 + i) // 12
        month_index = (today.month - 1 + i) % 12
        months.append(f"{monthList[month_index]} {today.year + year_offset}")
    return months

def calculatePayoffDate(amount, rate, payment):
    balance = amount
    monthlyRate = (rate / 100) / 12
    months = 0
    today = date.today()

    if payment <= balance * monthlyRate:
        return "The monthly payment must be greater than the first month's interest. Please check your numbers and try again.", 0

    while balance > 0:
        balance = balance * (1 + monthlyRate) - payment
        months += 1
        if balance < 0:
            balance = 0

    payoffYear = today.year + (today.month - 1 + months) // 12
    payoffMonth = (today.month - 1 + months) % 12 + 1
    return f"{monthList[payoffMonth - 1]} {today.day}, {payoffYear}", months
