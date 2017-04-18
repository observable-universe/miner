namespace ObservableUniverse.Generators {

  using UnityEngine;

  public class TriangleGenerator {
    public int[] run(Vector3[] verticies, int columnBound, int rowBound) {
      int vertColumns = columnBound + 1;
      int vertRows = rowBound + 1;
      int area = vertColumns * vertRows;

    	int haltIndex = area - vertColumns;

      // Counters and Cursors
      int index = 0;
      int cursor = 0;

  		// Triangles
      int[] triangles = new int[(columnBound * 6) * rowBound];

      int rowCount = 2;
      foreach(Vector3 vertex in verticies) {
        // Ensure that the cursor is not at the column bound
        if(cursor == (vertColumns * (rowCount - 1)) - 1) {
          cursor++;
          rowCount++;
        }

        // Ensure that the cursor is not at the maximum bound of the mesh
        if(cursor == haltIndex) {
          break;
        }

        // Triangle
        triangles[index] = cursor;
        triangles[++index] = cursor + 1;
        triangles[++index] = cursor + vertColumns;

        // Inverse Triangle
        triangles[++index] = cursor + vertColumns;
        triangles[++index] = cursor + 1;
        triangles[++index] = cursor + vertColumns + 1;

        cursor++;
        index++;
      }

      return triangles;
    }
  }

}
