import sys
import inspect
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

    def call_function(self, name, params):
        """Dynamically calls a method by name with given parameters, including submethods."""
        try:
            obj = self
            parts = name.split('.')

            # Traverse through nested objects (e.g., 'db.execute')
            for part in parts[:-1]:
                obj = getattr(obj, part, None)
                if obj is None:
                    return f"Error: '{part}' not found"

            method_name = parts[-1]
            if hasattr(obj, method_name):
                method = getattr(obj, method_name)
                if callable(method):
                    return method(*params)
                else:
                    return f"Error: '{method_name}' is not callable."
            else:
                return f"Error: Method '{method_name}' not found."

        except Exception as e:
            return f"Error: {e}"

    def execute(self, command, params=None):
        """Executes a database command with optional parameters."""
        try:
            return self.db.execute(command, params)
        except Exception:
            return None


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: No function name provided.")
        sys.exit(1)

    func_name = sys.argv[1]  # e.g., "db.execute" or "image.process_image"
    args = sys.argv[2:]  # Any additional arguments

    program = GeneralProgram()
    result = program.call_function(func_name, args)

    if result is not None:
        print(result)
