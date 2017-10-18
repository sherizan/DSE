using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SQLite;
using System.IO;

namespace Crawler
{
	public class Indexer
	{
		private Indexer() { }
		static Indexer() { }
		private static Indexer _instance = new Indexer();
		public static Indexer Instance { get { return _instance; } }

		SQLiteConnection db = null;
		SQLiteCommand command = null;
		const string dbFileName = "File Indices.sqlite";

		public void Init()
		{
			bool doesDBExists = File.Exists(dbFileName);

			// create new .sqlite file
			if (!doesDBExists) SQLiteConnection.CreateFile(dbFileName);

			db = new SQLiteConnection("Data Source=" + dbFileName + "; Version=3");
			db.Open();
			command = new SQLiteCommand(db);

			try
			{
				// check if our table exists
				command.CommandText = "SELECT 1 FROM files LIMIT 1;";
				command.ExecuteNonQuery();
			}
			catch (Exception e)
			{
				// table doesn't exist, create new table
				command.CommandText = "CREATE TABLE files (id TEXT, path TEXT, name TEXT)";
				command.ExecuteNonQuery();
			}
		}

		public void AddFileName(string filepath, string filename)
		{
			if (filename == "") return;

			command.CommandText = "INSERT INTO files (id, path, name) VALUES (HEX(RANDOMBLOB(16)), \"" + filepath + "\", \"" + filename + "\")";
			command.ExecuteNonQuery();
		}

		public List<string> GetAllFileNames()
		{
			List<string> filenames = new List<string>();

			command.CommandText = "SELECT * FROM filenames ORDER BY name DESC";
			SQLiteDataReader reader = command.ExecuteReader();
			while (reader.Read()) filenames.Add(reader["name"].ToString());

			return filenames;
		}
	}
}
