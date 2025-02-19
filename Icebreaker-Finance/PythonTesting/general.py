import sys
import inspect
import logging
from datetime import date
from databaseManagement import DatabaseManager
from dataVisualizationGenerator import definePlot
from imageProcessor import ImageProcessor
import switchObject as switch

class GeneralProgram:
    def __init__(self, host="mysql.neit.edu", port="5500", user="capstone_202520_winteriscoming",
                 password="Winteriscoming", database="capstone_202520_winteriscoming",
                 data=None, columns=None, image_path="", image_size=(), encoded_char_set="",
                 round_factor=0, mode='RGBA'):

        # Default to empty lists if not provided
        data = data or []
        columns = columns or []

        self.month_list = [
            "January", "February", "March", "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ]

        # Initialize components
        self.data_plot = self.plot(data, columns)
        self.db = self.db_manager(host, port, user, password, database)
        self.image = self.image_process(image_path, image_size, encoded_char_set, round_factor, mode)

        # Dynamically map class methods
        self.functions = switch.SwitchObject()
        for method in self.get_class_methods():
            self.functions[method] = getattr(self, method)
        self.functions.end = "No Method Found"

    def plot(self, data, columns):
        return definePlot(data, columns)

    def db_manager(self, host, port, user, password, database):
        return DatabaseManager(host, port, user, password, database)

    def image_process(self, image_path="", image_size=(), encoded_char_set="", round_factor=0, mode='RGBA'):
        return ImageProcessor(image_path=image_path, imageSize=image_size,
                              encoded_char_set=encoded_char_set, round=round_factor, mode=mode)

    def get_class_methods(self):
        """Returns a list of all public methods in this class."""
        methods = inspect.getmembers(self, predicate=inspect.ismethod)
        return [name for name, _ in methods if not name.startswith("__")]

    def library_methods_dictionary(self):
        """Returns a dictionary of available methods from different modules."""
        return {
            "DatabaseManager": self.get_class_methods(DatabaseManager),
            "definePlot": str(self.get_class_methods(definePlot) if inspect.isclass(definePlot) else "Function"),
            "ImageProcessor": str(self.get_class_methods(ImageProcessor))
        }

    def get_plot(self, table_name):
        """Fetches data from a table and generates a plot."""
        try:
            columns = self.db.execute(
                "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s;",
                (table_name,)
            )
            table_data = self.db.viewTable(table_name)

            if not table_data:
                logging.warning(f"No data found for table {table_name}.")
                return None

            self.data_plot.setData(table_data, columns)
            return self.data_plot

        except Exception as e:
            logging.error(f"Error in get_plot: {e}")
            return None

    def generate_bar_graph(self, debt_id, user_id):
        """Generates a bar graph of debt payments over time."""
        try:
            result = self.db.execute(
                "SELECT amount_owed, interest_rate, min_payment FROM debt_lookup WHERE debt_id = %s AND user_id = %s;",
                (debt_id, user_id)
            )

            if not result:
                logging.warning("No data found for the given debt ID and user ID.")
                return None

            amount, rate, min_payment = result[0]
            payoff_date, months = self.calculate_payoff_date(amount, rate, min_payment)
            debt_amounts = self.get_debt_amounts_each_month(amount, rate, min_payment, months)
            debt_months = self.month_from_now(months)
            return debt_months, debt_amounts

        except Exception as e:
            logging.error(f"Error in generate_bar_graph: {e}")
            return None

    def get_debt_amounts_each_month(self, amount, rate, min_payment, months):
        """Calculates remaining debt for each month."""
        balance = amount
        debt_amounts = []
        monthly_rate = (rate / 100) / 12

        for _ in range(months):
            balance = balance * (1 + monthly_rate) - min_payment
            balance = max(balance, 0)  # Ensure it never goes below zero
            debt_amounts.append(balance)

        return debt_amounts

    def month_from_now(self, month_distance):
        """Returns a list of future months from today."""
        months = []
        today = date.today()

        for i in range(month_distance):
            year_offset = (today.month - 1 + i) // 12
            month_index = (today.month - 1 + i) % 12
            months.append(f"{self.month_list[month_index]} {today.year + year_offset}")

        return months

    def calculate_payoff_date(self, amount, rate, payment):
        """Calculates when a debt will be paid off given a payment plan."""
        balance = amount
        monthly_rate = (rate / 100) / 12
        months = 0
        today = date.today()

        if payment <= balance * monthly_rate:
            return "The monthly payment must be greater than the first month's interest. Please check your numbers and try again.", 0

        while balance > 0:
            balance = balance * (1 + monthly_rate) - payment
            months += 1

        payoff_year = today.year + (today.month - 1 + months) // 12
        payoff_month = (today.month - 1 + months) % 12 + 1
        return f"{self.month_list[payoff_month - 1]} {today.day}, {payoff_year}", months

    def call_function(self, name, params):
        """Dynamically calls a method by name with given parameters."""
        try:
            if name in self.functions:
                return self.functions[name](*params)
            else:
                logging.error(f"Function '{name}' not found.")
                return "Function not found."

        except Exception as e:
            logging.error(f"Error calling function {name}: {e}")
            return None

    def execute(self, command, params=None):
        """Executes a database command with optional parameters."""
        try:
            return self.db.execute(command, params)
        except Exception as e:
            logging.error(f"Database execution error: {e}")
            return None


if __name__ == "__main__":
    if len(sys.argv) < 2:
        logging.error("No function name provided.")
        sys.exit(1)

    func_name = sys.argv[1]
    args = sys.argv[2:]

    program = GeneralProgram()
    result = program.call_function(func_name, args)

    if result is not None:
        print(result)
